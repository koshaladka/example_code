<?php

declare(strict_types=1);

namespace App\MoonShine\Pages\Blog;

use App\MoonShine\Resources\TagResource;
use Illuminate\Database\Eloquent\Builder;
use MoonShine\CKEditor\Fields\CKEditor;
use MoonShine\Components\Alert;
use MoonShine\Decorations\Block;
use MoonShine\Decorations\Column;
use MoonShine\Decorations\Divider;
use MoonShine\Decorations\Flex;
use MoonShine\Decorations\Grid;
use MoonShine\Decorations\Tab;
use MoonShine\Decorations\Tabs;
use MoonShine\Fields\Checkbox;
use MoonShine\Fields\Date;
use MoonShine\Fields\Field;
use MoonShine\Fields\ID;
use MoonShine\Fields\Image;
use MoonShine\Fields\Number;
use MoonShine\Fields\Relationships\BelongsTo;
use MoonShine\Fields\Slug;
use MoonShine\Fields\Switcher;
use MoonShine\Fields\Text;
use MoonShine\Fields\Textarea;
use MoonShine\Fields\TinyMce;
use MoonShine\Fields\Url;
use MoonShine\Pages\Crud\FormPage;

class BlogFormPage extends FormPage
{
    public function fields(): array
    {
        return [
            Number::make('Id','id')->hideOnIndex()->disabled(),
            Tabs::make([
                Tab::make('Основная информация', [
                    Block::make('Данные статьи', [
                        Textarea::make('Заголовок H1', 'title')->required(),
                        Slug::make('Символьный код. Заполняется автоматически после сохранения', 'slug')->from('title')->unique(),
                        Flex::make([
                            BelongsTo::make('Тэг', 'tag', 'name', resource: new TagResource())
                                ->valuesQuery(fn(Builder $query, Field $field) => $query->where('is_blog', '=', true))
                                ->required()
                                ->customAttributes(['class' => 'grow']),
                            Switcher::make('Активность','is_active')
                                ->default(true)
                                ->hint('Указывать активность, даже если дата начала позже сегодняшнего дня')
                                ->customAttributes(['class' => 'grow']),
                            Switcher::make('Показывать на главной','is_main')
                                ->default(true)
                                ->customAttributes(['class' => 'grow']),
                        ])->itemsAlign('end'),
                        Flex::make([
                            Date::make( 'Дата начала активности', 'date_start')
                                ->withTime()
                                ->default(date('Y-m-d H:i:s')),
                            Date::make( 'Дата окончания активности', 'date_end')
                                ->withTime(),
                        ]),
                        Flex::make([
                            Date::make('Дата статьи', 'date')
                                ->format('d.m.Y')
                                ->default(date('Y-m-d H:i:s')),
                            Number::make('Очередность', 'order'),
                        ]),
                        Flex::make([
                            Date::make( 'Дата создания', 'created_at')
                                ->withTime()
                                ->disabled(),
                            Date::make( 'Дата изменения', 'updated_at')
                                ->withTime()
                                ->disabled(),
                        ]),

                    ])
                ]),
                Tab::make('Изображения',[
                    Block::make('Изображения', [
                        Flex::make([
                            Block::make('Изображение в карточке',[
                                Image::make('(small)', 'image_sm')
                                    ->dir('image/blog')
                                    ->allowedExtensions(['jpg', 'jpeg', 'png'])
                                    ->removable()
                                    ->keepOriginalFileName(),
                                Alert::make('heroicons.information-circle')
                                    ->content('Соотношение сторон фотографии 16:9. Допустимые разрешения jpg, jpeg, png'),
                            ])->customAttributes(['class' => 'grow']),
                            Block::make('Изображение на внутренней странице', [
                                Image::make('(large)', 'image_lg')
                                    ->dir('image/blog')
                                    ->allowedExtensions(['jpg', 'jpeg', 'png'])
                                    ->removable()
                                    ->keepOriginalFileName(),
                                Alert::make('heroicons.information-circle')
                                    ->content('Соотношение сторон фотографии 21:9. Допустимые разрешения jpg, jpeg, png'),
                            ])->customAttributes(['class' => 'grow']),
                        ]),

                    ]),
                ]),
                Tab::make('Текст',[
                    Block::make('Текст статьи', [
                        TinyMce::make('Заголовок h1 берется из названия статьи. Для остальных заголовков необходимо использовать Заголовок 2, Заголовок 3', 'text'),
                    ]),
                ]),
                Tab::make('Промо-блок', [
                    Block::make([
                        Switcher::make('Отображать промо-блок ', 'is_promo_block')
                            ->hideOnIndex()
                            ->default(false),
                        Image::make('Промо изображение', 'promo_image')
                            ->dir('image/promo')
                            ->allowedExtensions(['jpg', 'jpeg', 'png'])
                            ->removable()
                            ->keepOriginalFileName(),
                        Alert::make('heroicons.information-circle')
                            ->content('Допустимые разрешения jpg, jpeg, png')
                            ->customAttributes(['class' => 'mb-8']),
                        Text::make('атрибут Alt', 'promo_image_alt'),
                        Url::make('Ссылка', 'promo_url')
                            ->hideOnIndex()
                            ->expansion('url'),
                    ]),
                ]),
                Tab::make('Автор', [
                    Block::make('Данные автора', [
                        Switcher::make('Показывать автора', 'is_author')->default(false),
                        Text::make('Имя автора', 'author_name'),
                        Text::make('Должность автора', 'author_position'),
                        Image::make('Фото автора', 'author_avatar')
                            ->dir('image/blog')
                            ->allowedExtensions(['jpg', 'jpeg', 'png'])
                            ->removable()
                            ->keepOriginalFileName(),
                    ])
                ]),
                Tab::make('CEO', [
                    Block::make('Настройки SEO информации', [
                        Block::make([
                            Flex::make([
                                Switcher::make('Закрыть от индексации', 'meta_is_noindex')
                                    ->default(false),
                                Switcher::make('nofollow', 'meta_is_nofollow')
                                    ->default(false),
                            ])->justifyAlign('start'),
                        ])->customAttributes(['class' => 'mb-4']),
                        Flex::make([
                            Block::make([
                                Switcher::make('Изменить meta_title', 'is_custom_meta_title')
                                    ->default(false)
                                    ->customAttributes(['class' => 'mb-4']),
                                Alert::make('heroicons.information-circle')
                                    ->content('По умолчанию: Заголовок новости (h1)'),
                                Text::make('Заголовок страницы Title', 'meta_title')
                                    ->default('Новости')
                                    ->showWhen('is_custom_meta_title', '=',  true),
                            ])->customAttributes(['class' => 'grow']),
                            Block::make([
                                Switcher::make('Изменить meta_description', 'is_custom_meta_description')
                                    ->default(false)
                                    ->customAttributes(['class' => 'mb-4']),
                                Alert::make('heroicons.information-circle')
                                    ->content('По умолчанию: первые 250 символов статьи'),
                                Textarea::make("Мета тег 'description",'meta_description')
                                    ->default('Новость')
                                    ->showWhen('is_custom_meta_description', '=',  true),
                            ])->customAttributes(['class' => 'grow']),

                        ])->justifyAlign('stretch')
                            ->itemsAlign('start')
                            ->customAttributes(['class' => 'mb-4']),

                        Textarea::make("Мета тег 'keywords'",'meta_keywords'),
                    ]),
                ]),
            ]),

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
