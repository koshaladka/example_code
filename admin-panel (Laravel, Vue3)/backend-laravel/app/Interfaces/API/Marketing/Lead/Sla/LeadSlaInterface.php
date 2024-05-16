<?php

namespace App\Interfaces\API\Marketing\Lead\Sla;

use App\Http\Requests\API\Marketing\Lead\Sla\LeadSlaStoreRequest;
use App\Http\Requests\API\Marketing\Lead\Sla\LeadSlaUpdateRequest;
use Illuminate\Http\{
    JsonResponse,
    Request
};
use OpenApi\Attributes as OA;

interface LeadSlaInterface
{
    #[OA\Get(
        path: '/api/v1/marketing/leads/slas',
        security: [['bearerAuth' => []]],
        tags: ['Leads_Sla'],
        description: 'Получение списка SLA по лидам',
        summary: 'Получение списка SLA по лидам',
        parameters: [
            new OA\Parameter(
                name: 'filters',
                description: "Фильтры json:
                 \n`statuses` - массив со slug стаусами лидов
                 \n`colors` - массив со slug цветов строк в ЛКП
                ",
                in: 'query',
                required: true,
                schema: new OA\Schema(
                    type: 'string',
                    example: '{{"statuses":["new","consultation"],"colors":["green"]}'
                )
            ),
            new OA\Parameter(
                name: 'limit',
                description: 'Количество выводимых элементов на странице',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'number',
                    example: '10'
                )
            ),
            new OA\Parameter(
                name: 'page',
                description: 'Номер страницы (от 1)',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'number',
                    example: '1'
                )
            ),
            new OA\Parameter(
                name: 'sort',
                description: 'Поле сортировки',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string',
                    example: 'time_from',
                    enum: [
                        'time_from',
                        'color',
                        'lead_status_id',
                    ]
                )
            ),
            new OA\Parameter(
                name: 'sort_type',
                description: 'Тип сортировки',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string',
                    example: 'asc',
                    enum: [
                        'asc',
                        'desc'
                    ]
                )
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Ok',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            type: 'boolean',
                            property: 'success',
                            example: true
                        ),
                        new OA\Property(
                            type: 'integer',
                            property: 'code',
                            example: 200
                        ),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(
                                    property: 'items',
                                    type: 'array',
                                    items: new OA\Items(
                                        properties: [
                                            new OA\Property(
                                                property: 'id',
                                                type: 'integer',
                                                description: 'Идентификатор SLA',
                                                example: 1,
                                            ),
                                            new OA\Property(
                                                property: 'time_from',
                                                type: 'integer',
                                                description: 'Время SLA',
                                                example: 2
                                            ),
                                            new OA\Property(
                                                property: 'color_name',
                                                type: 'string',
                                                description: 'Название цвета',
                                                example: 'Зеленый'
                                            ),
                                            new OA\Property(
                                                property: 'status',
                                                type: 'string',
                                                description: 'Название статуса лида',
                                                example: 'Зеленый'
                                            ),
                                        ]
                                    )
                                ),
                                new OA\Property(
                                    property: 'statuses',
                                    description: 'доступные для выбора статусы лидов (генерируется динамически в зависимости от созданных SLA)',
                                    type: 'array',
                                    items: new OA\Items(
                                        properties: [
                                            new OA\Property(
                                                property: 'name',
                                                type: 'string',
                                                description: 'Название статуса',
                                                example: 'Новый'
                                            ),
                                            new OA\Property(
                                                property: 'slug',
                                                type: 'string',
                                                description: 'Slug статуса',
                                                example: 'new'
                                            ),
                                        ]
                                    )
                                ),
                                new OA\Property(
                                    property: 'colors',
                                    description: 'доступные для выбора цвета (генерируется динамически в зависимости от созданных SLA)',
                                    type: 'array',
                                    items: new OA\Items(
                                        properties: [
                                            new OA\Property(
                                                property: 'name',
                                                type: 'string',
                                                description: 'Название статуса',
                                                example: 'Зеленый'
                                            ),
                                            new OA\Property(
                                                property: 'slug',
                                                type: 'string',
                                                description: 'Slug статуса',
                                                example: 'green'
                                            ),
                                        ]
                                    )
                                ),
                            ]
                        ),
                        new OA\Property(
                            property: 'meta',
                            type: 'object',
                            ref: '#/components/schemas/Pagination'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Нет доступа или токен недействителен',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            type: 'boolean',
                            property: 'success',
                            example: false
                        ),
                        new OA\Property(
                            type: 'integer',
                            property: 'code',
                            example: 401
                        ),
                        new OA\Property(
                            type: 'string',
                            property: 'message',
                            example: 'Нет доступа или токен недействителен'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 403,
                description: 'Нет доступа к данной странице',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            type: 'boolean',
                            property: 'success',
                            example: false
                        ),
                        new OA\Property(
                            type: 'integer',
                            property: 'code',
                            example: 403
                        ),
                        new OA\Property(
                            type: 'string',
                            property: 'message',
                            example: 'Нет доступа к данной странице'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 429,
                description: 'Превышен максимальный лимит обращений к API за короткое время',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            type: 'boolean',
                            property: 'success',
                            example: false
                        ),
                        new OA\Property(
                            type: 'integer',
                            property: 'code',
                            example: 429
                        ),
                        new OA\Property(
                            type: 'string',
                            property: 'message',
                            example: 'Превышен максимальный лимит обращений к API за короткое время'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Внутренняя ошибка сервера',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            type: 'boolean',
                            property: 'success',
                            example: false
                        ),
                        new OA\Property(
                            type: 'integer',
                            property: 'code',
                            example: 500
                        ),
                        new OA\Property(
                            type: 'string',
                            property: 'message',
                            example: 'Внутренняя ошибка сервера'
                        ),
                    ]
                )
            ),
        ]
    )]
    public function index(Request $request): JsonResponse;

    #[OA\Post(
        path: '/api/v1/marketing/leads/slas',
        security: [['bearerAuth' => []]],
        tags: ['Leads_Sla'],
        description: 'Создание SLA по лидам',
        summary: 'Создание SLA по лидам',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        type: 'string',
                        property: 'lead_status',
                        example: 'new',
                        description: 'Slug выбранного статуса лида'
                    ),
                    new OA\Property(
                        type: 'string',
                        property: 'color',
                        example: 'green',
                        description: 'Slug выбранного'
                    ),
                    new OA\Property(
                        type: 'integer',
                        property: 'time_from',
                        example: 5.30,
                        description: 'Время Sla',
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Создано',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            type: 'boolean',
                            property: 'success',
                            example: true
                        ),
                        new OA\Property(
                            type: 'integer',
                            property: 'code',
                            example: 201
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Нет доступа или токен недействителен',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            type: 'boolean',
                            property: 'success',
                            example: false
                        ),
                        new OA\Property(
                            type: 'integer',
                            property: 'code',
                            example: 401
                        ),
                        new OA\Property(
                            type: 'string',
                            property: 'message',
                            example: 'Нет доступа или токен недействителен'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 403,
                description: 'Нет доступа к данной странице',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            type: 'boolean',
                            property: 'success',
                            example: false
                        ),
                        new OA\Property(
                            type: 'integer',
                            property: 'code',
                            example: 403
                        ),
                        new OA\Property(
                            type: 'string',
                            property: 'message',
                            example: 'Нет доступа к данной странице'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 429,
                description: 'Превышен максимальный лимит обращений к API за короткое время',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            type: 'boolean',
                            property: 'success',
                            example: false
                        ),
                        new OA\Property(
                            type: 'integer',
                            property: 'code',
                            example: 429
                        ),
                        new OA\Property(
                            type: 'string',
                            property: 'message',
                            example: 'Превышен максимальный лимит обращений к API за короткое время'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Внутренняя ошибка сервера',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            type: 'boolean',
                            property: 'success',
                            example: false
                        ),
                        new OA\Property(
                            type: 'integer',
                            property: 'code',
                            example: 500
                        ),
                        new OA\Property(
                            type: 'string',
                            property: 'message',
                            example: 'Внутренняя ошибка сервера'
                        ),
                    ]
                )
            ),
        ]
    )]
    public function store(LeadSlaStoreRequest $request): JsonResponse;

    #[OA\Get(
        path: '/api/v1/marketing/leads/slas/{id}',
        security: [['bearerAuth' => []]],
        tags: ['Leads_Sla'],
        description: 'Получение детальной страницы SLA по лидам',
        summary: 'Получение детальной страницы SLA по лидам',
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Идентификатор SLA по лидам',
                in: 'path',
                required: true,
                schema: new OA\Schema(
                    type: 'number',
                    example: '1'
                )
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Ok',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            type: 'boolean',
                            property: 'success',
                            example: true
                        ),
                        new OA\Property(
                            type: 'integer',
                            property: 'code',
                            example: 200
                        ),
                        new OA\Property(
                            type: 'array',
                            property: 'data',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(
                                        property: 'id',
                                        type: 'integer',
                                        description: 'идентификатор SLA по лидам',
                                        example: 1
                                    ),
                                    new OA\Property(
                                        property: 'color',
                                        type: 'string',
                                        description: 'Slug цвета',
                                        example: 'green'
                                    ),
                                    new OA\Property(
                                        property: 'lead_status_slug',
                                        type: 'string',
                                        description: 'Slug статуса лида',
                                        example: 'new'
                                    ),
                                    new OA\Property(
                                        property: 'time_from',
                                        type: 'integer',
                                        description: 'кол-во часов Sla',
                                        example: 2.00
                                    ),
                                ]
                            )
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Нет доступа или токен недействителен',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            type: 'boolean',
                            property: 'success',
                            example: false
                        ),
                        new OA\Property(
                            type: 'integer',
                            property: 'code',
                            example: 401
                        ),
                        new OA\Property(
                            type: 'string',
                            property: 'message',
                            example: 'Нет доступа или токен недействителен'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 403,
                description: 'Нет доступа к данной странице',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            type: 'boolean',
                            property: 'success',
                            example: false
                        ),
                        new OA\Property(
                            type: 'integer',
                            property: 'code',
                            example: 403
                        ),
                        new OA\Property(
                            type: 'string',
                            property: 'message',
                            example: 'Нет доступа к данной странице'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Элемент не найден',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            type: 'boolean',
                            property: 'success',
                            example: false
                        ),
                        new OA\Property(
                            type: 'integer',
                            property: 'code',
                            example: 404
                        ),
                        new OA\Property(
                            type: 'string',
                            property: 'message',
                            example: 'SLA по лидам не найдена'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 429,
                description: 'Превышен максимальный лимит обращений к API за короткое время',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            type: 'boolean',
                            property: 'success',
                            example: false
                        ),
                        new OA\Property(
                            type: 'integer',
                            property: 'code',
                            example: 429
                        ),
                        new OA\Property(
                            type: 'string',
                            property: 'message',
                            example: 'Превышен максимальный лимит обращений к API за короткое время'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Внутренняя ошибка сервера',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            type: 'boolean',
                            property: 'success',
                            example: false
                        ),
                        new OA\Property(
                            type: 'integer',
                            property: 'code',
                            example: 500
                        ),
                        new OA\Property(
                            type: 'string',
                            property: 'message',
                            example: 'Внутренняя ошибка сервера'
                        ),
                    ]
                )
            ),
        ]
    )]
    public function show(int $id): JsonResponse;

    #[OA\Post(
        path: '/api/v1/marketing/leads/slas/{id}',
        security: [['bearerAuth' => []]],
        tags: ['Leads_Sla'],
        description: 'Редактирование SLA по лиду',
        summary: 'Редактирование SLA по лиду',
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Идентификатор SLA',
                in: 'path',
                required: true,
                schema: new OA\Schema(
                    type: 'number',
                    example: '1'
                )
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(
                            property: 'color',
                            type: 'string',
                            description: 'Slug цвета',
                            example: 'green'
                        ),
                        new OA\Property(
                            property: 'lead_status',
                            type: 'string',
                            description: 'Slug статуса лида',
                            example: 'new'
                        ),
                        new OA\Property(
                            property: 'time_from',
                            type: 'integer',
                            description: 'кол-во часов Sla',
                            example: 2
                        ),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 202,
                description: 'Обновлено',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            type: 'boolean',
                            property: 'success',
                            example: true
                        ),
                        new OA\Property(
                            type: 'integer',
                            property: 'code',
                            example: 202
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Нет доступа или токен недействителен',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            type: 'boolean',
                            property: 'success',
                            example: false
                        ),
                        new OA\Property(
                            type: 'integer',
                            property: 'code',
                            example: 401
                        ),
                        new OA\Property(
                            type: 'string',
                            property: 'message',
                            example: 'Нет доступа или токен недействителен'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 403,
                description: 'Нет доступа к данной странице',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            type: 'boolean',
                            property: 'success',
                            example: false
                        ),
                        new OA\Property(
                            type: 'integer',
                            property: 'code',
                            example: 403
                        ),
                        new OA\Property(
                            type: 'string',
                            property: 'message',
                            example: 'Нет доступа к данной странице'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Элемент не найден',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            type: 'boolean',
                            property: 'success',
                            example: false
                        ),
                        new OA\Property(
                            type: 'integer',
                            property: 'code',
                            example: 404
                        ),
                        new OA\Property(
                            type: 'string',
                            property: 'message',
                            example: 'SLA по лидам не найдено'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 429,
                description: 'Превышен максимальный лимит обращений к API за короткое время',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            type: 'boolean',
                            property: 'success',
                            example: false
                        ),
                        new OA\Property(
                            type: 'integer',
                            property: 'code',
                            example: 429
                        ),
                        new OA\Property(
                            type: 'string',
                            property: 'message',
                            example: 'Превышен максимальный лимит обращений к API за короткое время'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Внутренняя ошибка сервера',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            type: 'boolean',
                            property: 'success',
                            example: false
                        ),
                        new OA\Property(
                            type: 'integer',
                            property: 'code',
                            example: 500
                        ),
                        new OA\Property(
                            type: 'string',
                            property: 'message',
                            example: 'Внутренняя ошибка сервера'
                        ),
                    ]
                )
            ),
        ]
    )]
    public function update(int $id, LeadSlaUpdateRequest $request): JsonResponse;

    #[OA\Delete(
        path: '/api/v1/marketing/leads/slas/{id}',
        security: [['bearerAuth' => []]],
        tags: ['Leads_Sla'],
        description: 'Удаление SLA по лидам',
        summary: 'Удаление SLA по лидам',
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Идентификатор SLA по лидам',
                in: 'path',
                required: true,
                schema: new OA\Schema(
                    type: 'number',
                    example: '1'
                )
            ),
        ],
        responses: [
            new OA\Response(
                response: 202,
                description: 'Accepted',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            type: 'boolean',
                            property: 'success',
                            example: true
                        ),
                        new OA\Property(
                            type: 'integer',
                            property: 'code',
                            example: 202
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Нет доступа или токен недействителен',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            type: 'boolean',
                            property: 'success',
                            example: false
                        ),
                        new OA\Property(
                            type: 'integer',
                            property: 'code',
                            example: 401
                        ),
                        new OA\Property(
                            type: 'string',
                            property: 'message',
                            example: 'Нет доступа или токен недействителен'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 403,
                description: 'Нет доступа к данной странице',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            type: 'boolean',
                            property: 'success',
                            example: false
                        ),
                        new OA\Property(
                            type: 'integer',
                            property: 'code',
                            example: 403
                        ),
                        new OA\Property(
                            type: 'string',
                            property: 'message',
                            example: 'Нет доступа к данной странице'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Элемент не найден',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            type: 'boolean',
                            property: 'success',
                            example: false
                        ),
                        new OA\Property(
                            type: 'integer',
                            property: 'code',
                            example: 404
                        ),
                        new OA\Property(
                            type: 'string',
                            property: 'message',
                            example: 'Элемент не найден'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 429,
                description: 'Превышен максимальный лимит обращений к API за короткое время',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            type: 'boolean',
                            property: 'success',
                            example: false
                        ),
                        new OA\Property(
                            type: 'integer',
                            property: 'code',
                            example: 429
                        ),
                        new OA\Property(
                            type: 'string',
                            property: 'message',
                            example: 'Превышен максимальный лимит обращений к API за короткое время'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Внутренняя ошибка сервера',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            type: 'boolean',
                            property: 'success',
                            example: false
                        ),
                        new OA\Property(
                            type: 'integer',
                            property: 'code',
                            example: 500
                        ),
                        new OA\Property(
                            type: 'string',
                            property: 'message',
                            example: 'Внутренняя ошибка сервера'
                        ),
                    ]
                )
            ),
        ]
    )]
    public function destroy($id): JsonResponse;
}
