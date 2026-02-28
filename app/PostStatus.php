<?php

namespace App;

enum PostStatus: int
{
    case DRAFT = 0;
    case PUBLISHED = 1;

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::PUBLISHED => 'Published',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'zinc',
            self::PUBLISHED => 'green',
        };
    }
}
