<?php

namespace App\Filament\Widgets\Concerns;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Illuminate\Database\Eloquent\Model;

trait InteractsWithWidgetActions
{
    use InteractsWithActions {
        getDefaultActionRecord as protected getFilamentDefaultActionRecord;
        getDefaultActionModel as protected getFilamentDefaultActionModel;
    }
    use InteractsWithUserImpersonation;

    public function getDefaultActionRecord(Action $action): ?Model
    {
        return $this->resolveImpersonationDefaultActionRecord($action)
            ?? $this->getFilamentDefaultActionRecord($action);
    }

    /**
     * @return class-string<Model>|null
     */
    public function getDefaultActionModel(Action $action): ?string
    {
        return $this->resolveImpersonationDefaultActionModel($action)
            ?? $this->getFilamentDefaultActionModel($action);
    }
}
