<?php

namespace Different\Dwfw\app\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Schema;

class AccountScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        // ezt felvenném a modellbe, hogy ne így kelljen mindig csekkolni, hogy ez egy accountos tábla
        // vagy egy közös ősmodell, vagy pedig valami azonnal kikérdezhető érték: hasAccount = true
        if (!Schema::hasColumn($model->getTable(), 'account_id')) {
            return;
        }
        if (session('account_id') != -1) {
            $builder->whereAccountId(session('account_id'));
        } else {
            $builder->whereIn('account_id', session('account_ids'));
        }
    }
}
