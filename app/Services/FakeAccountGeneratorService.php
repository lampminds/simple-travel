<?php

namespace App\Services;

use App\Models\Account;
use App\Models\AccountPerson;
use App\Models\AccountType;
use App\Models\LmpCity;
use App\Models\Person;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;
use Spatie\Permission\PermissionRegistrar;

/**
 * Creates tenant accounts (operator / agency) with an owner user for local development and testing.
 */
final class FakeAccountGeneratorService
{
    private const PASSWORD = 'fl1c5ch1';

    /** @var array<string, array{label: string, slug: string, account_type_code: string}> */
    private const TYPES = [
        'operator' => [
            'label' => 'Operador',
            'slug' => 'operador',
            'account_type_code' => 'operator',
        ],
        'agency' => [
            'label' => 'Agencia',
            'slug' => 'agencia',
            'account_type_code' => 'agency',
        ],
    ];

    public function __construct(
        private readonly ReplicateDefaultRolesToAccountService $replicateDefaultRolesToAccount,
        private readonly AccountStartupService $accountStartupService,
        private readonly PermissionRegistrar $permissionRegistrar,
    ) {
    }

    /**
     * @return list<array{company_name: string, nick: string, email: string, password: string, city: string|null}>
     */
    public function generate(string $type, int $count): array
    {
        $type = $this->normalizeType($type);
        $config = self::TYPES[$type];

        if ($count < 1) {
            throw new InvalidArgumentException('Count must be at least 1.');
        }

        $accountTypeId = AccountType::query()
            ->where('code', $config['account_type_code'])
            ->where('active', true)
            ->value('id');

        if ($accountTypeId === null) {
            throw new RuntimeException("Account type [{$config['account_type_code']}] not found in cat_account_types.");
        }

        $contactDepartmentId = (int) DB::table('cat_contact_departments')
            ->where('code', 'management')
            ->where('active', true)
            ->value('id');

        $contactPositionId = (int) DB::table('cat_contact_positions')
            ->where('code', 'director')
            ->where('active', true)
            ->value('id');

        if ($contactDepartmentId < 1 || $contactPositionId < 1) {
            throw new RuntimeException('Contact department/position catalog is missing (run DatabaseSeeder first).');
        }

        $created = [];

        for ($i = 0; $i < $count; $i++) {
            $created[] = $this->createOne(
                config: $config,
                accountTypeId: (int) $accountTypeId,
                contactDepartmentId: $contactDepartmentId,
                contactPositionId: $contactPositionId,
            );
        }

        return $created;
    }

    /**
     * @param  array{label: string, slug: string, account_type_code: string}  $config
     * @return array{company_name: string, nick: string, email: string, password: string, city: string|null}
     */
    private function createOne(
        array $config,
        int $accountTypeId,
        int $contactDepartmentId,
        int $contactPositionId,
    ): array {
        $identity = $this->uniqueIdentity($config);

        $city = LmpCity::query()->inRandomOrder()->first();
        $cityLabel = $city?->name;

        $bundle = DB::transaction(function () use (
            $identity,
            $config,
            $accountTypeId,
            $contactDepartmentId,
            $contactPositionId,
            $city,
        ) {
            $person = Person::create([
                'name' => $identity['owner_name'],
            ]);

            $user = User::create([
                'name' => $identity['owner_name'],
                'email' => $identity['email'],
                'password' => Hash::make(self::PASSWORD),
                'email_verified_at' => now(),
                'activation_state' => User::ACTIVATION_ACTIVE,
            ]);

            $user->persons()->attach($person->id);

            $account = Account::create([
                'nick' => $identity['nick'],
                'name' => $identity['company_name'],
                'commercial_name' => $identity['company_name'],
                'email' => $identity['email'],
                'phone' => fake()->phoneNumber(),
                'address_line1' => fake()->streetAddress(),
                'address_line2' => fake()->boolean(30) ? fake()->secondaryAddress() : null,
                'city_id' => $city?->id,
                'postal_code' => fake()->postcode(),
            ]);

            $user->accounts()->attach($account->id);

            $this->permissionRegistrar->setPermissionsTeamId((int) $account->id);
            $this->replicateDefaultRolesToAccount->replicateTo((int) $account->id, null, (int) $user->id);
            $user->assignRole('owner');

            throw_unless(
                $user->fresh()->hasRole('owner'),
                RuntimeException::class,
                'Fake account generation must assign the owner role.',
            );

            AccountPerson::create([
                'account_id' => $account->id,
                'person_id' => $person->id,
                'link_type' => AccountPerson::LINK_MEMBER,
                'contact_department_id' => $contactDepartmentId,
                'contact_position_id' => $contactPositionId,
                'is_primary' => true,
                'is_active' => true,
                'is_public_contact' => false,
                'is_preferred_contact_mode' => false,
            ]);

            $account->accountTypes()->attach([$accountTypeId]);

            $this->accountStartupService->runForNewAccount((int) $account->id, (int) $user->id);

            return [
                'company_name' => $identity['company_name'],
                'nick' => $identity['nick'],
                'email' => $identity['email'],
                'password' => self::PASSWORD,
                'city' => $city?->name,
            ];
        });

        return $bundle;
    }

    /**
     * @param  array{label: string, slug: string, account_type_code: string}  $config
     * @return array{company_name: string, nick: string, email: string, owner_name: string}
     */
    private function uniqueIdentity(array $config): array
    {
        for ($attempt = 0; $attempt < 50; $attempt++) {
            $suffix = $this->randomSuffix();
            $companyName = "{$config['label']}-{$suffix}";
            $nick = Str::slug($companyName);
            $domainSlug = Str::lower("{$config['slug']}-".Str::lower($suffix));
            $email = "owner@{$domainSlug}.com";
            $ownerName = 'Owner '.$config['slug'].' '.$suffix;

            if (
                Account::query()->where('nick', $nick)->exists()
                || User::query()->where('email', $email)->exists()
            ) {
                continue;
            }

            return [
                'company_name' => $companyName,
                'nick' => $nick,
                'email' => $email,
                'owner_name' => $ownerName,
            ];
        }

        throw new RuntimeException('Could not generate a unique fake account identity after several attempts.');
    }

    private function randomSuffix(): string
    {
        $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $suffix = '';

        for ($i = 0; $i < 4; $i++) {
            $suffix .= $letters[random_int(0, 25)];
        }

        return $suffix;
    }

    private function normalizeType(string $type): string
    {
        $type = Str::lower(trim($type));

        return match ($type) {
            'operator', 'operators', 'operador', 'operadores' => 'operator',
            'agency', 'agencies', 'agencia', 'agencias' => 'agency',
            default => throw new InvalidArgumentException(
                "Invalid account type [{$type}]. Use operator or agency."
            ),
        };
    }
}
