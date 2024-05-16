<?php

declare(strict_types=1);

namespace App\MoonShine\Pages\Blog;

use App\MoonShine\Resources\TagResource;
use MoonShine\Fields\Date;
use MoonShine\Fields\Relationships\BelongsTo;
use MoonShine\Fields\Switcher;
use MoonShine\Pages\Crud\IndexPage;
use MoonShine\Fields\Text;

class BlogIndexPage extends IndexPage
{
    public function fields(): array
    {
        return [
            Text::make('ID', 'id')->sortable(),
            Text::make('Заголовок', 'title')
                ->changePreview(fn($item) => str($item)->limit(50)),
            Switcher::make('Активность','is_active')
                ->updateOnPreview(),
            Text::make('Очередность', 'order')->sortable(),
            BelongsTo::make('Тэг', 'tag', 'name', resource: new TagResource()),
            Date::make('Дата', 'date')
                ->format('d.m.Y')
                ->sortable(),
        ];
    }

    protected function topLayer(): array
    {
        return [
            ...parent::topLayer()
        ];
    }

    protected function mainLayer(): array
    {
        return [
            ...parent::mainLayer()
        ];
    }

    protected function bottomLayer(): array
    {
        return [
            ...parent::bottomLayer()
        ];
    }

}
