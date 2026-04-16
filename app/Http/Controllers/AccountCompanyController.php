<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\LmpCity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class AccountCompanyController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();
        abort_unless($user !== null, 401);

        abort_unless($user->hasRoleForCurrentAccount('owner'), 403);

        $account = $user->currentAccount();
        abort_unless($account instanceof Account, 404);

        $cityId = old('city_id', $account->city_id);
        $currentCity = null;
        if (is_numeric($cityId)) {
            $currentCity = LmpCity::query()
                ->with(['state.country'])
                ->find((int) $cityId);
        }

        return view('account.company-edit', [
            'account' => $account,
            'currentCity' => $currentCity,
        ]);
    }

    public function cityDetails(Request $request, int $cityId): JsonResponse
    {
        $user = $request->user();
        abort_unless($user !== null, 401);
        abort_unless($user->hasRoleForCurrentAccount('owner'), 403);

        $city = LmpCity::query()
            ->with(['state.country'])
            ->find($cityId);

        if (! $city) {
            return response()->json(['message' => 'City not found.'], 404);
        }

        return response()->json([
            'id' => $city->id,
            'name' => $city->name,
            'state' => $city->state?->name,
            'country' => $city->state?->country?->name,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user !== null, 401);

        abort_unless($user->hasRoleForCurrentAccount('owner'), 403);

        $account = $user->currentAccount();
        abort_unless($account instanceof Account, 404);

        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'commercial_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'address_line1' => ['nullable', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'city_id' => ['nullable', 'integer'],
            'postal_code' => ['nullable', 'string', 'max:255'],
        ]);

        if (! empty($data['city_id']) && ! LmpCity::query()->whereKey((int) $data['city_id'])->exists()) {
            return back()
                ->withErrors(['city_id' => 'La ciudad seleccionada no es válida.'])
                ->withInput();
        }

        $account->fill($data);
        $account->save();

        return redirect()
            ->route('account.dashboard')
            ->with('status', 'Los datos de la empresa se han actualizado.');
    }
}

