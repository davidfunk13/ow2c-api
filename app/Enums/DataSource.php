<?php

namespace App\Enums;

enum DataSource: string
{
    case Manual = 'manual';
    case Scraped = 'scraped';
    case Imported = 'imported';
    case ScreenshotOcr = 'screenshot_ocr';
}
