<?php

use App\Models\AccountAddress;
use App\Models\Address;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Copy the single location stored on accounts into addresses, then drop those columns.
     */
    public function up(): void
    {
        $accounts = DB::table('accounts')->orderBy('id')->get([
            'id',
            'address_line1',
            'address_line2',
            'city_id',
            'postal_code',
        ]);

        foreach ($accounts as $account) {
            $line1 = trim((string) ($account->address_line1 ?? ''));
            $line2 = trim((string) ($account->address_line2 ?? ''));
            $postal = trim((string) ($account->postal_code ?? ''));
            $cityId = is_numeric($account->city_id) ? (int) $account->city_id : null;

            if ($line1 === '' && $line2 === '' && $postal === '' && $cityId === null) {
                continue;
            }

            $payload = [
                'address_line_1' => $line1,
                'address_line_2' => $line2 !== '' ? $line2 : null,
                'postal_code' => $postal !== '' ? $postal : null,
                'city_id' => null,
                'city' => null,
                'state_id' => null,
                'state' => null,
                'country_id' => null,
            ];

            if ($cityId !== null) {
                $city = DB::connection('addons')->table('lmp_cities')->where('id', $cityId)->first();
                if ($city !== null) {
                    $state = $city->state_id
                        ? DB::connection('addons')->table('lmp_states')->where('id', $city->state_id)->first()
                        : null;
                    $payload['city_id'] = (int) $city->id;
                    $payload['city'] = $city->name;
                    $payload['state_id'] = $city->state_id;
                    $payload['state'] = $state->name ?? null;
                    $payload['country_id'] = $state->country_id ?? null;
                }
            }

            $address = Address::query()->create($payload);
            AccountAddress::query()->create([
                'account_id' => (int) $account->id,
                'address_id' => $address->id,
                'type' => 'primary',
            ]);
        }

        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn(['address_line1', 'address_line2', 'city_id', 'postal_code']);
        });
    }

    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->string('postal_code')->nullable();
        });
    }
};
