# Permission and authorization model

This application uses [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission) with **teams** enabled. The team foreign key is **`account_id`** (see `config/permission.php`), aligned with multi-tenant accounts.

## Reserved platform account

A **single reserved account** represents the Spatie “team” for **platform-wide** roles and permissions (staff who can act across tenants). By convention:

- Its primary key is **`1`** by default (configurable via `PLATFORM_ACCOUNT_ID` in `.env`).
- There is **no** `is_system` flag on `accounts`; the rule is **do not use this row as a normal tenant** for business data.
- The row is created by migrations when missing (`database/migrations/2026_04_01_000001_ensure_platform_account_exists.php`).

Spatie does not support a `null` team. Using a dedicated account row keeps **one** permission system and avoids a parallel authorization layer.

## How team resolution works per request

Middleware `App\Http\Middleware\SetPermissionsTeamForRequest` runs after the session is available and sets `PermissionRegistrar::setPermissionsTeamId()`:

1. **If the authenticated user belongs to the platform account** (pivot `account_user` includes the platform id), the team id is **always** the platform account id.  
   - That way `hasRole()`, `can()`, etc. evaluate **platform** roles regardless of which account is selected for **data** context (e.g. session `current_account_id`).
2. **Otherwise**, the team is the **current session account** (if valid for the user), or the **first** linked account by id, same as before.

So: **authorization** (Spatie) vs **tenant data scope** (session / scopes) are separate: platform staff keep global permissions while still switching accounts for data.

## Database layout (high level)

- **`user_permissions`** / **`user_roles`**: permission and role definitions; **roles** have `account_id` when teams are on (per-team role names).
- **`user_model_has_roles`** / **`user_model_has_permissions`**: pivot includes `account_id` (team) for the assignment.
- **Permissions** are global by name + guard; **roles** are per team (`account_id`).

Platform roles are rows in `user_roles` with **`account_id` = platform account id**.

## User model helpers

- **`User::belongsToPlatformAccount()`** — `true` if the user is linked to the platform account in `account_user`.
- **`User::roleNamesAcrossTeams()`** — aggregates role names from all team assignments for that user (used for listing users without depending on the current registrar team).

## Filament administration

- **`RoleResource`** (`App\Filament\Resources\Authorization`): manages only roles whose **`account_id`** is the platform account. Create/edit attach **permissions** (`CheckboxList`).
- **`PermissionResource`**: CRUD for global permission names (guard `web`, typically hidden).
- Navigation group: **Authorization** (`filament.resources.nav_authorization`).
- Policies **`RolePolicy`** and **`PermissionPolicy`** allow access only when **`belongsToPlatformAccount()`** is true; for roles, the record must belong to the platform team.

## User edit and role sync

In `UserResource`, the form uses a **Accounts & roles** tab with a repeater: **one row per account**, each row selects the account and the **role IDs for that Spatie team** (`account_id` on `user_model_has_roles`). Saving runs `User::syncAccountMemberships()`, which syncs `account_user` and then calls `syncRoles()` with `PermissionRegistrar::setPermissionsTeamId()` for each account. Global role definitions (`user_roles.account_id` null) remain assignable in any team, consistent with registration flows.

## Configuration reference

| Item | Location |
|------|----------|
| Platform account id | `config('permission.platform_account_id')` ← `PLATFORM_ACCOUNT_ID` env (default `1`) |
| Team column name | `config('permission.column_names.team_foreign_key')` → `account_id` |
| Table names | `config('permission.table_names.*')` (`user_roles`, `user_permissions`, …) |

## Operational checklist

1. Run migrations so the platform account row exists.
2. Link staff users to the **platform account** in `account_user`.
3. Create **permissions** and **platform roles** in Filament (Authorization group), then assign roles to users (e.g. via `UserResource`) with the correct team context (see user edit flow above).

## Related code

- `App\Http\Middleware\SetPermissionsTeamForRequest`
- `App\Models\User` (`belongsToPlatformAccount`, `roleNamesAcrossTeams`)
- `App\Policies\RolePolicy`, `App\Policies\PermissionPolicy`
- `App\Filament\Resources\Authorization\RoleResource`, `PermissionResource`
