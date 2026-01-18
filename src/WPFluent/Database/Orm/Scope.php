<?php

namespace Adirectory\Framework\Database\Orm;

interface Scope
{
    /**
     * Apply the scope to a given Orm query builder.
     *
     * @param  \Adirectory\Framework\Database\Orm\Builder  $builder
     * @param  \Adirectory\Framework\Database\Orm\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model);
}
