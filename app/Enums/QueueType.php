<?php

namespace App\Enums;

enum QueueType: string
{
    case CompetitiveRoleQueue = 'competitive_role_queue';
    case CompetitiveOpenQueue = 'competitive_open_queue';
    case QuickPlay = 'quick_play';
    case Arcade = 'arcade';
    case Custom = 'custom';
}
