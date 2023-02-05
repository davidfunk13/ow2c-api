<?php

namespace App\Http\Resources\Session;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SessionCollection extends ResourceCollection
{
    public function toArray($request): array|\Illuminate\Support\Collection|\JsonSerializable|Arrayable
    {
        return $this->collection;
    }
}
