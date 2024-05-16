<?php

declare(strict_types=1);

namespace App\MoonShine\Pages;

use App\Models\Blog;
use App\Models\Form;
use App\Models\Partners\Partner;
use App\Models\Promotion;
use MoonShine\Decorations\Column;
use MoonShine\Decorations\Flex;
use MoonShine\Decorations\Grid;
use MoonShine\Metrics\DonutChartMetric;
use MoonShine\Metrics\LineChartMetric;
use MoonShine\Metrics\ValueMetric;
use MoonShine\Pages\Page;

class Dashboard extends Page
{
    public function breadcrumbs(): array
    {
        return [
            '#' => $this->title()
        ];
    }

    public function title(): string
    {
        return $this->title ?: 'Добро пожаловать';
    }

    public function components(): array
	{
		return [
            Grid::make([
                ValueMetric::make('Количество новостей')
                ->value(Blog::query()->count())
                ->columnSpan(6),

                ValueMetric::make('Количество акций')
                    ->value(Promotion::query()->count())
                ->columnSpan(6),
            ])->customAttributes(['class' => 'mb-4']),
            Grid::make([
                DonutChartMetric::make('Блог')
                    ->values(['Активны' => Blog::query()->where('is_active', true)->count(),
                        'Неактивны' => Blog::query()->where('is_active', false)->count()])
                    ->columnSpan(6),
                DonutChartMetric::make( 'Акции')
                    ->values(['Активны' => Promotion::query()->where('is_active', true)->count(),
                        'Неактивны' => Promotion::query()->where('is_active', false)->count()])
                    ->columnSpan(6)
            ])->customAttributes(['class' => 'mb-4']),
            Grid::make([
                ValueMetric::make('Отправлено форм ОС')
                    ->value(Form::query()->count())
                    ->columnSpan(6),
                ValueMetric::make('Отправлено форм на партнерство')
                    ->value(Partner::query()->count())
                    ->columnSpan(6),
            ])->customAttributes(['class' => 'mb-4']),



        ];
	}
}
