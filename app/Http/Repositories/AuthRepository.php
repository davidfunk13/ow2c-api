<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Battletag;
use CoderCat\JWKToPEM\JWKConverter;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

class AuthRepository

{
   protected function setFields(Battletag &$battletag, array $options): void
    {
        return null;
    }

}
