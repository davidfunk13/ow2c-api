<?php

declare(strict_types=1);

namespace App\Http\Repositories;

use App\Http\Controllers\NotFoundResponseTrait;
use App\Models\Session;
use Illuminate\Database\Query\Builder;

class SessionRepository
{
    use NotFoundResponseTrait;

    public function setFields(Session &$session, array $options): void
    {
        $session->name = $options['name'];
    }
    protected function handleRelations(Builder $qb, array $relations, array $options): void
    {
        if (isset($options['with']) && is_array($options['with']) && count($options['with']) > 0) {
            $relations = array_merge($relations, $options['with']);
        }

        $qb->with($relations);
    }
}
