<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\Blog;
use App\MoonShine\Pages\Blog\BlogIndexPage;
use App\MoonShine\Pages\Blog\BlogFormPage;
use App\MoonShine\Pages\Blog\BlogDetailPage;

use MoonShine\Enums\ClickAction;
use MoonShine\Resources\ModelResource;

class BlogResource extends ModelResource
{
    protected string $model = Blog::class;

    protected string $title = 'Блог, Новости';
    public string $column = 'title';
    protected ?ClickAction $clickAction = ClickAction::EDIT;

    public function pages(): array
    {
        return [
            BlogIndexPage::make($this->title()),
            BlogFormPage::make(
                $this->getItemID()
                    ? __('moonshine::ui.edit')
                    : __('moonshine::ui.add')
            ),
            BlogDetailPage::make(__('moonshine::ui.show')),
        ];
    }

    public function rules(Model $item): array
    {
        return [];
    }

    public function getBadge(): string
    {
        return strval(Blog::query()->count());
    }


}
