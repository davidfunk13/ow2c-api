<?php

namespace App\Enums;

enum MapType: string
{
    case Control = 'control';
    case Escort = 'escort';
    case Hybrid = 'hybrid';
    case Push = 'push';
    case Flashpoint = 'flashpoint';
}
