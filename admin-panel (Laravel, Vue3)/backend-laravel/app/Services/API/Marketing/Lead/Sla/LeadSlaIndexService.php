<?php

namespace App\Services\API\Marketing\Lead\Sla;

use App\Http\Resources\API\Marketing\Lead\Sla\LeadSlaCollection;
use App\Helpers\BaseHelper;
use App\Models\LeadSla;
use Illuminate\Http\Response;
use Exception;


class LeadSlaIndexService extends LeadSlaService
{
    public function execute(
        int $page,
        int $limit,
        ?string $filters,
        ?string $sort = null,
        ?string $sort_type = null
    ): array {

        $items = LeadSla::query()
            ->select(
                'lead_slas.*',

                'lead_statuses.id as status_id',
                'lead_statuses.name as status_name',
                'lead_statuses.slug as status_slug',
            )
            ->leftJoin(
                'lead_statuses',
                'lead_slas.lead_status_id',
                'lead_statuses.id'
            );

        // Filter values (for frontend)
        $statuses = [];
        $colors = [];
        $this->getFilterValues(
            items: $items,
            statuses: $statuses,
            colors: $colors
        );

        // Filtrate
        $isFiltered  = false;
        $items = $this->filtrate(
            items: $items,
            filters: $filters,
            isFiltered: $isFiltered
        );

        if (is_null($items))
            throw new Exception('Ошибка фильтрации', Response::HTTP_INTERNAL_SERVER_ERROR);

        // Sort
        if (!empty($sort) && !BaseHelper::getSortedItems(
            items: $items,
            columns: self::SORT_COLUMNS,
            sort: $sort,
            type: $sort_type
        ))
            throw new Exception('Некорректный тип сортировки или сама сортировка', Response::HTTP_BAD_REQUEST);

        // Pagination
        $items = BaseHelper::initPaginate(
            items: $items,
            page: $page,
            limit: $limit,
            isFiltered: $isFiltered
        );

        if (is_null($items))
            throw new Exception('Ошибка пагинации', Response::HTTP_INTERNAL_SERVER_ERROR);

        $response = [];
        $response['items'] = new LeadSlaCollection($items);
        $response['statuses'] = $statuses;
        $response['colors'] = $colors;

        return [
            'success' => true,
            'code' => Response::HTTP_OK,
            'data' => $response,
            'meta' => [
                'current_page' => $items->currentPage(),
                'total_pages' => $items->lastPage(),
                'total_items' => $items->total(),
                'limit' => $limit
            ]
        ];
    }

    /**
     * Сопоставление столбцов для сортировки
     */
    private const SORT_COLUMNS = [
        'time_from' => null,
        'color' => null,
        'lead_status_id' => null,
    ];

    /**
     * Фильтрация
     */
    private function filtrate(
        mixed $items,
        ?string $filters,
        bool &$isFiltered
    ): mixed {
        $filters = BaseHelper::parseJson($filters);

        if (is_null($filters))
            return null;

        // Statuses
        if (isset($filters['statuses']) && !empty($filters['statuses']) && is_array($filters['statuses'])) {
            $isFiltered = true;

            $items = $items->whereIn('lead_statuses.slug', $filters['statuses']);
        }

        // Colors
        if (isset($filters['colors']) && !empty($filters['colors']) && is_array($filters['colors'])) {
            $isFiltered = true;

            $items = $items->whereIn('lead_slas.color', $filters['colors']);
        }

        return $items;
    }

    /**
     * Получение значений для фильтров
     */
    private function getFilterValues(mixed $items, mixed &$statuses, mixed &$colors,): void
    {
        $collection = $items->get();

        // Statuses
        $collection
            ->whereNotNull('status_id')
            ?->unique('status_id')
            ?->pluck('status_name', 'status_slug')
            ?->map(function (string $statusName, string $statusSlug) use (&$statuses) {
                return $statuses[] = [
                    'name' => $statusName,
                    'slug' => $statusSlug
                ];
            }) ?? [];


        // Colors
        $collection
            ->whereNotNull('color')
            ?->unique('color')
            ?->pluck('color')
            ?->map(function (string $colorCode) use (&$colors) {
                return $colors[] = [
                    'name' => $colorCode,
                ];
            }) ?? [];
    }

}
