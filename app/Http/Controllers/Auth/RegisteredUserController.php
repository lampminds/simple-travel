<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Middleware\RecordLastLogin;
use App\Http\Middleware\SetPermissionsTeamForRequest;
use App\Models\Account;
use App\Models\AccountCategory;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\Permission\PermissionRegistrar;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $companyTypes = AccountCategory::query()
            ->byGroup('type')
            ->with('translations')
            ->where('active', true)
            ->ordered()
            ->get()
            ->mapWithKeys(fn ($cat) => [
                $cat->id => ['name' => $cat->name, 'description' => $cat->description ?? ''],
            ]);

        return view('auth.signup', [
            'companyTypes' => $companyTypes,
        ]);
    }

    /**
     * Handle an incoming registration request.
     * Creates an Account and a User linked to it.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:8',
            'company_name' => [
                'required',
                'string',
                'max:255',
                function (string $attribute, mixed $value, \Closure $fail) {
                    $value = trim($value);
                    if (Account::where('name', $value)->orWhere('commercial_name', $value)->exists()) {
                        $fail(__('validation.custom.company_name.unique'));
                    }
                },
            ],
            'company_type' => [
                'required',
                Rule::exists('cat_account_categories', 'id')->where('group', 'type'),
            ],
        ], [
            'email.unique' => __('validation.custom.email.unique_user'),
        ]);

        $companyName = $request->string('company_name')->trim();
        $nick = $this->uniqueNickFromCompanyName($companyName);

        $newAccountId = DB::transaction(function () use ($request, $companyName, $nick) {
            $account = Account::create([
                'nick' => $nick,
                'name' => $companyName,
                'commercial_name' => $companyName,
                'email' => $request->email,
            ]);

            $account->categories()->attach($request->company_type);

            $user = User::create([
                'name' => Str::title($request->string('name')->trim()),
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user->accounts()->attach($account->id);

            app(PermissionRegistrar::class)->setPermissionsTeamId($account->id);
            $user->assignRole('owner');

            event(new Registered($user));

            Auth::login($user);

            return $account->id;
        });

        $request->session()->put(RecordLastLogin::SESSION_KEY, true);
        $request->session()->put(SetPermissionsTeamForRequest::SESSION_CURRENT_ACCOUNT_ID, $newAccountId);

        return redirect()->route('verification.notice');
    }

    /**
     * Generate a unique nick for the account from the company name.
     */
    private function uniqueNickFromCompanyName(string $companyName): string
    {
        $base = Str::slug(Str::limit($companyName, 35));
        $base = $base ?: 'empresa';
        $nick = $base;
        $suffix = 0;
        while (Account::where('nick', $nick)->exists()) {
            $suffix++;
            $nick = $base . '-' . $suffix;
        }
        return $nick;
    }
}
