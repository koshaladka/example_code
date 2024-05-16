<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Tag;
use App\Services\Blog\BlogService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BlogController extends Controller
{
    /**
     * @OA\Post(
     *     path="/blog/section",
     *     summary="Получение карточек блога",
     *     tags={"Блог"},
     *     @OA\RequestBody(
     *          required=true,
     *          description="Тело запроса",
     *          @OA\JsonContent(
     *              @OA\Property(property="number", type="integer", example=1, description="Количество карточек до 100"),
     *              @OA\Property(property="currentId", type="integer", example=1, description="Id текущей акции при запросе")
     *          )
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешный ответ",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                   @OA\Property(property="id", type="integer", example=1),
     *                   @OA\Property(property="slug", type="string", example=1),
     *                   @OA\Property(property="tag_id", type="integer", example=1),
     *                   @OA\Property(property="order", type="integer", example=1, nullable=true),
     *                   @OA\Property(property="title", type="string", example="Трейд-ин на ТСД"),
     *                   @OA\Property(property="date_start", type="string", format="date-time", example="2023-09-22 00:00:00"),
     *                   @OA\Property(property="date_end", type="string", format="date-time", example="2024-09-22 00:00:00"),
     *                   @OA\Property(property="image_sm", type="string", example="http://localhost/storage/image/promo/promo_1.png"),
     *                   @OA\Property(
     *                       property="tags",
     *                       type="object",
     *                       @OA\Property(property="id", type="integer", example=1),
     *                       @OA\Property(property="name", type="string", example="Акция")
     *                   )
     *               )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Ошибка",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function getBlogSection(Request $request)
    {
        $request->validate([
            'number' => 'required|integer|between:1,100',
            'currentId' => 'nullable|numeric',
        ]);

        $number = $request->input('number');


        $blogService = new BlogService;
        $blogData = $blogService->getByNumber($number, $request->currentId);

        return response()->json($blogData);
    }


    /**
     * @OA\Get(
     *      path="/blog/{slug}",
     *      summary="Получение статьи по slug",
     *      tags={"Блог"},
     *      @OA\Parameter(
     *          name="slug",
     *          in="path",
     *          required=true,
     *          description="SLUG статьи",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Успешный ответ",
     *          @OA\JsonContent(
     *              type="object",
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="slug", type="string", example="kak-otkrit-cvetochnii-bisness-2"),
     *             @OA\Property(property="is_active", type="boolean"),
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="date", type="string"),
     *             @OA\Property(property="is_main", type="boolean"),
     *             @OA\Property(property="image_lg", type="string"),
     *             @OA\Property(property="text", type="string"),
     *             @OA\Property(property="is_author", type="boolean"),
     *             @OA\Property(property="author_name", type="string"),
     *             @OA\Property(property="author_position", type="string"),
     *             @OA\Property(property="author_avatar", type="string"),
     *             @OA\Property(property="is_promo_block", type="boolean"),
     *             @OA\Property(property="promo_image", type="string"),
     *             @OA\Property(property="promo_url", type="string"),
     *             @OA\Property(property="meta_title", type="string"),
     *             @OA\Property(property="meta_description", type="string"),
     *             @OA\Property(property="meta_keywords", type="string"),
     *             @OA\Property(property="meta_is_noindex", type="string"),
     *             @OA\Property(property="meta_is_nofollow", type="string"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Blog post not found"
     *      )
     * )
     */
    public function getBlogBySlug($slug)
    {
        $blogService = new BlogService;
        $blog = $blogService->getBySlug($slug);

        if (!$blog) {
            return response()->json(['error' => 'Blog not found'], 404);
        }

        return response()->json($blog);
    }

    /**
     * @OA\Get(
     *     path="/blog/tags",
     *     summary="Получение тегов блога",
     *     tags={"Блог"},
     *     @OA\Response(
     *         response=200,
     *         description="Успешный ответ",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", description="Идентификатор тега"),
     *                 @OA\Property(property="code", type="string", description="Код тега"),
     *                 @OA\Property(property="name", type="string", description="Название тега"),
     *                 @OA\Property(property="names", type="string", description="Название тега во множественном числе")
     *             )
     *         )
     *     )
     * )
     */
    public function getBlogTags()
    {
        $tags = Tag::where('is_blog', true)
            ->orderBy('order', 'asc')
            ->get()
            ->makeHidden(['is_blog', 'created_at', 'updated_at']);
        return response()->json($tags);
    }

    /**
     * @OA\Post(
     *     path="/blog/paginate",
     *     summary="Получение данных для разводящей страницы блога (с пагинацией)",
     *     tags={"Блог"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"page", "per_page"},
     *                 @OA\Property(property="page", type="integer", description="Номер страницы", example=1),
     *                 @OA\Property(property="per_page", type="integer", description="Количество элементов на странице", example=10),
     *                 @OA\Property(property="tag_code", type="string", description="Код тега", example="news"),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешный ответ",
     *         @OA\JsonContent(
     *             type="object",
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(property="current_page", type="integer"),
         *              @OA\Property(property="data", type="array",
         *                 @OA\Items(
         *                  @OA\Property(property="id", type="integer"),
         *                  @OA\Property(property="slug", type="string"),
         *                  @OA\Property(property="image_sm", type="string"),
         *                  @OA\Property(property="title", type="string"),
         *                  @OA\Property(property="tag_id", type="integer"),
         *                  @OA\Property(property="tag", type="object",
         *                      @OA\Property(property="id", type="integer"),
         *                      @OA\Property(property="name", type="string"),
         *                      @OA\Property(property="code", type="string"),
         *                  ),
         *              )),
         *              @OA\Property(property="first_page_url", type="string"),
         *              @OA\Property(property="from", type="integer"),
         *              @OA\Property(property="last_page", type="integer"),
         *              @OA\Property(property="last_page_url", type="string"),
         *              @OA\Property(property="links", type="array", @OA\Items(
         *                  @OA\Property(property="url", type="string"),
         *                  @OA\Property(property="label", type="string"),
         *                  @OA\Property(property="active", type="boolean"),
         *              )),
         *              @OA\Property(property="next_page_url", type="string"),
         *              @OA\Property(property="path", type="string"),
         *              @OA\Property(property="per_page", type="integer"),
         *              @OA\Property(property="prev_page_url", type="string"),
         *              @OA\Property(property="to", type="integer"),
         *              @OA\Property(property="total", type="integer"),
     *              ),
     *              @OA\Property(
     *                  property="ceo",
     *                  type="object",
     *                  @OA\Property(property="meta_title", type="string"),
     *                  @OA\Property(property="meta_description", type="string"),
     *                  @OA\Property(property="meta_keywords", type="string"),
     *                  @OA\Property(property="meta_is_noindex", type="boolean")),
     *                  @OA\Property(property="meta_is_nofollow", type="boolean")),
     *              )
     *
     *         ),
     *     ),
     * )
     */
    public function getBlogPaginate(Request $request)
    {
        $request->validate([
            'page' => 'required|integer|min:1',
            'per_page' => 'required|integer|min:3',
            'tag_code' => [
                'nullable',
                'string',
                Rule::exists('tags', 'code'),
            ],
        ]);

        $blogService = new BlogService;
        $result = $blogService->getPaginate($request);
        return $result;

//        return response()->json($result['data'], $result['code']);
    }
}
