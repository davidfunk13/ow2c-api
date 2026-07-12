<?php

namespace App\Enums;

enum Role: string
{
    case Tank = 'tank';
    case Damage = 'damage';
    case Support = 'support';
}
