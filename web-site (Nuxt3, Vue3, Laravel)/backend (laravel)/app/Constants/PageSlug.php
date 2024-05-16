<?php

namespace App\Constants;

use Illuminate\Support\Collection;

final class PageSlug
{
    public const MAIN =  'main';
    public const KNOWLEDGE =  'knowledge';
    public const TEAM = 'team';
    public const BLOG = 'blog';
    public const PROMOTIONS = 'promotions';


    public const ALL_NAMES = [
        self::MAIN=> 'Главная страница',
        self::KNOWLEDGE=> 'База знаний',
        self::TEAM => 'Команда',
        self::BLOG => 'Блог (разводящая страница)',
        self::PROMOTIONS => 'Акции (разводящая страница)',
    ];

    public static function getName(string $slug): string
    {
        return self::ALL_NAMES[$slug] ?? 'Неизвестная страница';
    }
}
