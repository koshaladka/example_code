<?php

namespace App\Services\Blog;

use App\Constants\PageSlug;
use App\Helpers\DateHelper;
use App\Helpers\ImageHelper;
use App\Models\Blog;
use App\Models\CeoPage;
use App\Services\Common\DateService;
use Illuminate\Http\Request;

class BlogService
{
    /**
     * Получение блока новостей
     */
    public function getByNumber($number, $currentId)
    {
        try {
            $news = Blog::where('is_main', true)
                ->where('id', '!=', $currentId)
                ->where('is_active', true)
                ->where('date_start', '<=', now())
                ->orderBy('created_at', 'desc')
                ->orderBy('order', 'asc')
                ->take($number)
                ->select('id', 'slug', 'tag_id', 'order', 'title', 'date', 'image_sm')
                ->with(['tag' => function ($query) {
                    $query->select('id', 'name', 'code');
                }])
                ->get();

            $news->each(function ($news) {
                $news->image_sm = ImageHelper::getImageUrl($news->image_sm);
                $news->date = DateHelper::getDateD_MMM_YYYY($news->date);
            });

            return $news;
        } catch(\Exception $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * Получение по ID
     */
    public function getBySlug($slug)
    {
        $blog = Blog::where('slug', $slug)->first();

        $blog->image_lg = ImageHelper::getImageUrl($blog->image_lg);
        $blog->author_avatar = ImageHelper::getImageUrl($blog->author_avatar);
        $blog->promo_image = ImageHelper::getImageUrl($blog->promo_image);
        $blog->date= DateHelper::getDateD_MMM_YYYY($blog->date);
        $blog->makeHidden(['date_start',
                            'tag_id',
                            'order',
                            'date_end',
                            'image_sm',
                            'created_at',
                            'updated_at']);

        return $blog;
    }

    /**
     * Получение для разводящей страницы при пагинации
     */
    public function getPaginate(Request $request)
    {
        $blogs = Blog::select('id', 'slug', 'image_sm', 'title', 'tag_id', 'date')
            ->with(['tag' => function ($query) {
                $query->select('id', 'name', 'code');
            }])
            ->orderBy('created_at', 'desc')
            ->orderBy('order', 'asc');

        if ($request->has('tag_code')) {
            $blogs->whereHas('tag', function ($query) use ($request) {
                $query->where('code', $request->tag_code);
            });
        }

        $blogs = $blogs->paginate($request->per_page);

        $blogs->each(function ($blog) {
            $blog->image_sm = ImageHelper::getImageUrl($blog->image_sm);
            $blog->date= DateHelper::getDateD_MMM_YYYY($blog->date);
        });

        $data['data'] = $blogs;
        $data['ceo'] = CeoPage::where('slug', PageSlug::BLOG)
            ->first()
            ->makeHidden('id', 'created_at', 'updated_at', 'slug');

        return $data;


    }


}
