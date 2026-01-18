<?php

namespace Adirectory\Framework\Database\Orm;

use BadMethodCallException;
use Adirectory\Framework\Support\Str;
use Adirectory\Framework\Database\Orm\Relations\HasMany;
use Adirectory\Framework\Database\Orm\Relations\MorphOneOrMany;

/**
 * @template TIntermediateModel of \Adirectory\Framework\Database\Orm\Model
 * @template TDeclaringModel of \Adirectory\Framework\Database\Orm\Model
 */
class PendingHasThroughRelationship
{
    /**
     * The root model that the relationship exists on.
     *
     * @var TDeclaringModel
     */
    protected $rootModel;

    /**
     * The local relationship.
     *
     * @var \Adirectory\Framework\Database\Orm\Relations\HasMany<TIntermediateModel, TDeclaringModel>|\Adirectory\Framework\Database\Orm\Relations\HasOne<TIntermediateModel, TDeclaringModel>
     */
    protected $localRelationship;

    /**
     * Create a pending has-many-through or has-one-through relationship.
     *
     * @param  TDeclaringModel  $rootModel
     * @param  \Adirectory\Framework\Database\Orm\Relations\HasMany<TIntermediateModel, TDeclaringModel>|\Adirectory\Framework\Database\Orm\Relations\HasOne<TIntermediateModel, TDeclaringModel>  $localRelationship
     */
    public function __construct($rootModel, $localRelationship)
    {
        $this->rootModel = $rootModel;

        $this->localRelationship = $localRelationship;
    }

    /**
     * Define the distant relationship that this model has.
     *
     * @template TRelatedModel of \Adirectory\Framework\Database\Orm\Model
     *
     * @param  string|(callable(TIntermediateModel): (\Adirectory\Framework\Database\Orm\Relations\HasOne<TRelatedModel, TIntermediateModel>|\Adirectory\Framework\Database\Orm\Relations\HasMany<TRelatedModel, TIntermediateModel>|\Adirectory\Framework\Database\Orm\Relations\MorphOneOrMany<TRelatedModel, TIntermediateModel>))  $callback
     * @return (
     *     $callback is string
     *     ? \Adirectory\Framework\Database\Orm\Relations\HasManyThrough<\Adirectory\Framework\Database\Orm\Model, TIntermediateModel, TDeclaringModel>|\Adirectory\Framework\Database\Orm\Relations\HasOneThrough<\Adirectory\Framework\Database\Orm\Model, TIntermediateModel, TDeclaringModel>
     *     : (
     *         $callback is callable(TIntermediateModel): \Adirectory\Framework\Database\Orm\Relations\HasOne<TRelatedModel, TIntermediateModel>
     *         ? \Adirectory\Framework\Database\Orm\Relations\HasOneThrough<TRelatedModel, TIntermediateModel, TDeclaringModel>
     *         : \Adirectory\Framework\Database\Orm\Relations\HasManyThrough<TRelatedModel, TIntermediateModel, TDeclaringModel>
     *     )
     * )
     */
    public function has($callback)
    {
        if (is_string($callback)) {
            $callback = fn () => $this->localRelationship->getRelated()->{$callback}();
        }

        $distantRelation = $callback($this->localRelationship->getRelated());

        if ($distantRelation instanceof HasMany) {
            $returnedRelation = $this->rootModel->hasManyThrough(
                get_class($distantRelation->getRelated()),
                get_class($this->localRelationship->getRelated()),
                $this->localRelationship->getForeignKeyName(),
                $distantRelation->getForeignKeyName(),
                $this->localRelationship->getLocalKeyName(),
                $distantRelation->getLocalKeyName(),
            );
        } else {
            $returnedRelation = $this->rootModel->hasOneThrough(
                get_class($distantRelation->getRelated()),
                get_class($this->localRelationship->getRelated()),
                $this->localRelationship->getForeignKeyName(),
                $distantRelation->getForeignKeyName(),
                $this->localRelationship->getLocalKeyName(),
                $distantRelation->getLocalKeyName(),
            );
        }

        if ($this->localRelationship instanceof MorphOneOrMany) {
            $returnedRelation->where($this->localRelationship->getQualifiedMorphType(), $this->localRelationship->getMorphClass());
        }

        return $returnedRelation;
    }

    /**
     * Handle dynamic method calls into the model.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (Str::startsWith($method, 'has')) {
            return $this->has(Str::of($method)->after('has')->lcfirst()->toString());
        }

        throw new BadMethodCallException(sprintf(
            'Call to undefined method %s::%s()', static::class, $method
        ));
    }
}
