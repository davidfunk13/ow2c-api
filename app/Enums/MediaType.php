<?php

namespace App\Enums;

enum MediaType: string
{
    case ScoreboardScreenshot = 'scoreboard_screenshot';
    case ProfileScreenshot = 'profile_screenshot';
    case GeneralScreenshot = 'general_screenshot';
    case VideoClip = 'video_clip';
}
