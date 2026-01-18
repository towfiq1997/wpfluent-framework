<?php

namespace Adirectory\Framework\Database\Orm;

/**
 * @template TCollection of \Adirectory\Framework\Database\Orm\Collection
 */
trait HasCollection
{
    /**
     * Create a new Orm Collection instance.
     *
     * @param  array<array-key, \Adirectory\Framework\Database\Orm\Model>  $models
     * @return TCollection
     */
    public function newCollection(array $models = [])
    {
        return new static::$collectionClass($models);
    }
}
