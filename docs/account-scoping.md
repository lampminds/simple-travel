# Account scoping conventions

Simple Travel uses `account_id` in two different ways. Mixing them causes subtle bugs (wrong copies on registration, Spatie resolving the wrong team, import wizards reading empty catalogs).

## Summary

| Pattern | `account_id` value | Meaning | Examples |
|---------|-------------------|---------|----------|
| **Platform account** | **`1`** (config: `PLATFORM_ACCOUNT_ID`) | A real row in `accounts`, reserved for the system. Not a normal tenant. | Spatie roles (`user_roles`), `account_user` for staff, role replication source |
| **System catalog** | **`NULL`** | Template rows with no tenant; copied or imported into real accounts | `todo_tasks` (setup checklist), `service_transfer_vehicle_types`, `service_transfer_locations`, `parameter_values` (system scope), `currency_rates` (official rates) |
| **Tenant data** | **`<tenant id>`** | Belongs to one customer account | `services`, account-specific price lists, per-tenant todo tasks after onboarding copy |

**Rule of thumb:** if Spatie or `account_user` needs it, use the **platform account id** (default `1`). If it is seed data meant to be cloned into new accounts and never “owned” by account 1, use **`NULL`**.

## Reserved platform account (`account_id = 1`)

- Configured in `config('permission.platform_account_id')` / env `PLATFORM_ACCOUNT_ID` (default `1`).
- Must exist in `accounts` (see migration `ensure_platform_account_exists`).
- **Do not** use this account for normal business operations (services, clients, operator catalog, etc.).
- **Do** use it for:
  - Platform role definitions in `user_roles` (`account_id = 1`).
  - Staff membership in `account_user` linked to account `1`.
  - Source rows when `ReplicateDefaultRolesToAccountService` clones default roles into a new tenant.

Spatie teams require a **non-null** `account_id` on `user_model_has_roles` and `user_model_has_permissions` (part of the primary key). A `null` “global team” is not used. See [permissions.md](permissions.md).

When a new account is created, roles are **replicated** from the platform account to the new `account_id`; permissions on those roles and user pivots are remapped accordingly (`App\Services\ReplicateDefaultRolesToAccountService`).

## System catalog (`account_id IS NULL`)

- Rows are **not** tied to any tenant.
- New accounts receive **copies** with their own `account_id` (not references to `null` rows).
- Queries for templates use `whereNull('account_id')`, not `where('account_id', 1)`.

### Tables using `NULL` (non-exhaustive)

| Table | Seeder / bootstrap | Copy on new account |
|-------|-------------------|---------------------|
| `todo_tasks` | `TodoTasksTableSeeder` | `AccountStartupService` → `TodoCategoryCopyTasksToAccountService::copyFromSystemCatalog()` |
| `service_transfer_vehicle_types` | `ServiceTransferVehicleTypesCatalogSeeder` | Wizard / account UI via `ServiceTransferVehicleCatalogBootstrapService` |
| `service_transfer_locations` | `ServiceTransferLocationsFozDoIguacuAccount1Seeder`, `ServiceTransferLocationsSanMartinDeLosAndesSeeder` | Wizard via `ServiceTransferLocationCatalogBootstrapService` |
| `parameter_values` | `ParameterValuesTableSeeder` (system scope) | Per-parameter reader logic |
| `currency_rates` | System rates | Tenant overrides use non-null `account_id` |

Migrations for these tables define `account_id` as **nullable** where applicable.

### What does *not* use `NULL` for “system”

| Area | Why |
|------|-----|
| `user_roles`, `user_model_has_roles` | Spatie teams; platform account `1` is the template team |
| `user_permissions` | Global by name + guard (no `account_id` on definition) |
| `account_user` | Real membership; platform staff link to account `1` |
| Most `cat_*` tables | No `account_id` column (global reference data) |

## Adding new features

1. **Will Spatie assign roles/permissions?** → Platform account id on role definitions; tenant id on copies and pivots.
2. **Is it seed/template data cloned into every new tenant?** → `account_id = NULL` on templates; nullable FK in migration; copy service uses `whereNull('account_id')`.
3. **Is it normal tenant-owned data?** → Non-null `account_id` = tenant id only.

## Related documentation and config

- [transfer-location-types.md](transfer-location-types.md) — how to extend `service_transfer_location_types` when seeding new cities
- [permissions.md](permissions.md) — Spatie teams, middleware, Filament authorization
- `config/permission.php` — `platform_account_id`, `platform_template_user_id`
- `.env.example` — `PLATFORM_ACCOUNT_ID=1`

## Related code

- `App\Services\ReplicateDefaultRolesToAccountService` — roles: platform account → new tenant
- `App\Services\AccountStartupService` — todo tasks: system catalog → new tenant
- `App\Services\TodoCategoryCopyTasksToAccountService`
- `App\Services\ServiceTransferVehicleCatalogBootstrapService`
- `App\Services\ServiceTransferLocationCatalogBootstrapService`
- `App\Http\Middleware\SetPermissionsTeamForRequest`
