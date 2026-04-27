<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

/**
 * Clones roles, role↔permission pivots, and optional user direct assignments from the template
 * account (default platform id 1) to a new tenant so Spatie team-scoped resolution works.
 */
final class ReplicateDefaultRolesToAccountService
{
    public function __construct(
        private readonly PermissionRegistrar $permissionRegistrar
    ) {
    }

    /**
     * @param  int  $targetAccountId  The new or destination account (user_roles.account_id / team id).
     * @param  int|null  $templateAccountId  Source template; defaults to config permission.platform_account_id.
     * @param  int|null  $targetUserId  If set, replaces this user's `user_model_has_*` for the team with a copy
     *                                   of the platform template user, with role_ids remapped to the target account.
     */
    public function replicateTo(int $targetAccountId, ?int $templateAccountId = null, ?int $targetUserId = null): void
    {
        $template = $templateAccountId ?? (int) config('permission.platform_account_id', 1);

        if ($targetAccountId < 1 || $targetAccountId === $template) {
            return;
        }

        $teamKey = $this->permissionRegistrar->teamsKey;
        $pivotTable = config('permission.table_names.role_has_permissions');
        $pivotRole = $this->permissionRegistrar->pivotRole;
        $pivotPermission = $this->permissionRegistrar->pivotPermission;
        $modelTableRoles = config('permission.table_names.model_has_roles');
        $modelTablePermissions = config('permission.table_names.model_has_permissions');
        $columnNames = config('permission.column_names');
        $morphKey = $columnNames['model_morph_key'] ?? 'model_id';
        $pivotModelRole = $this->permissionRegistrar->pivotRole;

        $sourceRoles = Role::query()
            ->where($teamKey, $template)
            ->where('name', '!=', 'admin')
            ->orderBy('id')
            ->get();

        if ($sourceRoles->isEmpty()) {
            return;
        }

        DB::transaction(function () use (
            $sourceRoles,
            $targetAccountId,
            $template,
            $teamKey,
            $pivotTable,
            $pivotRole,
            $pivotPermission,
            $modelTableRoles,
            $modelTablePermissions,
            $morphKey,
            $pivotModelRole,
            $targetUserId
        ) {
            $hasAnyTargetRole = Role::query()
                ->where($teamKey, $targetAccountId)
                ->exists();

            if (! $hasAnyTargetRole) {
                $oldIdToNewId = [];
                foreach ($sourceRoles as $role) {
                    $new = $role->replicate();
                    $new->setAttribute($teamKey, $targetAccountId);
                    $new->save();
                    $oldIdToNewId[$role->getKey()] = $new->getKey();
                }

                if ($oldIdToNewId === []) {
                    return;
                }

                $pivotRows = DB::table($pivotTable)
                    ->whereIn($pivotRole, array_keys($oldIdToNewId))
                    ->get();

                $rows = [];
                foreach ($pivotRows as $row) {
                    $oldRoleId = (int) $row->{$pivotRole};
                    $newRoleId = $oldIdToNewId[$oldRoleId] ?? null;
                    if ($newRoleId === null) {
                        continue;
                    }
                    $rows[] = [
                        $pivotPermission => (int) $row->{$pivotPermission},
                        $pivotRole => $newRoleId,
                    ];
                }

                foreach (array_chunk($rows, 100) as $chunk) {
                    if ($chunk !== []) {
                        DB::table($pivotTable)->insert($chunk);
                    }
                }
            }

            $sourceToTargetRoleId = $this->sourceToTargetRoleIdMap(
                (int) $template,
                $targetAccountId,
                $sourceRoles
            );

            if ($sourceToTargetRoleId === [] || $targetUserId === null) {
                return;
            }

            $templateUserId = $this->resolveTemplateUserId($template);
            if ($templateUserId === null || $targetUserId < 1) {
                return;
            }

            $this->syncUserModelPivotsFromTemplateUser(
                (int) $template,
                $targetAccountId,
                $templateUserId,
                $targetUserId,
                $sourceToTargetRoleId,
                $modelTableRoles,
                $modelTablePermissions,
                $morphKey,
                $pivotModelRole,
                $teamKey
            );
        });

        $this->permissionRegistrar->forgetCachedPermissions();
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Collection<int, Role>  $sourceRoles
     * @return array<int, int>  source user_roles.id => target user_roles.id
     */
    private function sourceToTargetRoleIdMap(int $templateAccountId, int $targetAccountId, $sourceRoles): array
    {
        $teamKey = $this->permissionRegistrar->teamsKey;
        $map = [];
        foreach ($sourceRoles as $src) {
            $target = Role::query()
                ->where($teamKey, $targetAccountId)
                ->where('name', $src->name)
                ->where('guard_name', $src->guard_name)
                ->first();
            if ($target !== null) {
                $map[(int) $src->getKey()] = (int) $target->getKey();
            }
        }

        return $map;
    }

    private function resolveTemplateUserId(int $templateAccountId): ?int
    {
        $configured = config('permission.platform_template_user_id');
        if (is_int($configured) && $configured > 0) {
            return $configured;
        }
        if (is_string($configured) && ctype_digit($configured) && (int) $configured > 0) {
            return (int) $configured;
        }

        $id = (int) DB::table('account_user')
            ->where('account_id', $templateAccountId)
            ->orderBy('user_id')
            ->value('user_id');

        return $id > 0 ? $id : null;
    }

    /**
     * @param  array<int, int>  $sourceToTargetRoleId
     */
    private function syncUserModelPivotsFromTemplateUser(
        int $templateAccountId,
        int $targetAccountId,
        int $templateUserId,
        int $targetUserId,
        array $sourceToTargetRoleId,
        string $modelTableRoles,
        string $modelTablePermissions,
        string $morphKey,
        string $pivotModelRole,
        string $teamKey
    ): void {
        $userModelType = $this->morphTypeForUser();

        // If this user already has any Spatie pivot rows for the team, do not replace them
        // (avoids a second run—e.g. after email verification—wiping a registration-time assignRole).
        if (
            DB::table($modelTableRoles)
                ->where($teamKey, $targetAccountId)
                ->where('model_type', $userModelType)
                ->where($morphKey, $targetUserId)
                ->exists()
        ) {
            return;
        }

        if (
            DB::table($modelTablePermissions)
                ->where($teamKey, $targetAccountId)
                ->where('model_type', $userModelType)
                ->where($morphKey, $targetUserId)
                ->exists()
        ) {
            return;
        }

        DB::table($modelTableRoles)
            ->where($teamKey, $targetAccountId)
            ->where('model_type', $userModelType)
            ->where($morphKey, $targetUserId)
            ->delete();

        DB::table($modelTablePermissions)
            ->where($teamKey, $targetAccountId)
            ->where('model_type', $userModelType)
            ->where($morphKey, $targetUserId)
            ->delete();

        $sourceRoleRows = DB::table($modelTableRoles)
            ->where($teamKey, $templateAccountId)
            ->where('model_type', $userModelType)
            ->where($morphKey, $templateUserId)
            ->get();

        $toInsertRoles = [];
        foreach ($sourceRoleRows as $row) {
            $oldRid = (int) $row->{$pivotModelRole};
            $newRid = $sourceToTargetRoleId[$oldRid] ?? null;
            if ($newRid === null) {
                continue;
            }
            $toInsertRoles[] = [
                $pivotModelRole => $newRid,
                'model_type' => $userModelType,
                $morphKey => $targetUserId,
                $teamKey => $targetAccountId,
            ];
        }
        if ($toInsertRoles !== []) {
            foreach (array_chunk($toInsertRoles, 100) as $chunk) {
                DB::table($modelTableRoles)->insert($chunk);
            }
        }

        $sourcePermRows = DB::table($modelTablePermissions)
            ->where($teamKey, $templateAccountId)
            ->where('model_type', $userModelType)
            ->where($morphKey, $templateUserId)
            ->get();

        $pivotPerm = $this->permissionRegistrar->pivotPermission;
        $toInsertPerms = [];
        foreach ($sourcePermRows as $row) {
            $toInsertPerms[] = [
                $pivotPerm => (int) $row->{$pivotPerm},
                'model_type' => $userModelType,
                $morphKey => $targetUserId,
                $teamKey => $targetAccountId,
            ];
        }
        if ($toInsertPerms !== []) {
            foreach (array_chunk($toInsertPerms, 100) as $chunk) {
                DB::table($modelTablePermissions)->insert($chunk);
            }
        }
    }

    private function morphTypeForUser(): string
    {
        return (string) (new User)->getMorphClass();
    }
}
