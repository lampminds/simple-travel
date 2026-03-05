<?php
namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class BelongsToAccountScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        if ($account_id = auth()->user()?->account_id) {
            $builder->where($model->getTable() . '.account_id', $account_id);
        }
    }
}
