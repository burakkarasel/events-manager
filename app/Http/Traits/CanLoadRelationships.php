<?php

namespace App\Http\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait CanLoadRelationships
{
    public function loadRelationships(
        Model | Builder | HasMany $for,
        ?array $relations = null,
    ): Model | Builder | HasMany
    {
        // ?? checks left hand side if left hand side is null it returns the right hand side
        $relations = $relations ?? $this->relations ?? [];
        foreach ($relations as $relation) {
            $for->when(
                $this->shouldIncludeRelation($relation),
                // if the given for type is Model it means model is already loaded so, we use load method of the model
                // otherwise it's a query builder and, we add with method to load the relations
                fn(Builder $q) => $for instanceof Model ? $for->load($relation) : $q->with($relation)
            );
        }

        return $for;
    }

    protected function shouldIncludeRelation(string $relation): bool {
        $include = request()->query("include");
        if(!$relation) return false;

        $relations = array_map("trim", explode(",", $include));
        return in_array($relation, $relations, true);
    }
}
