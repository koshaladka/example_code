<?php

namespace App\Http\Controllers\API\Marketing\Lead\Sla;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Marketing\Lead\Sla\LeadSlaStoreRequest;
use App\Http\Requests\API\Marketing\Lead\Sla\LeadSlaUpdateRequest;
use App\Interfaces\API\Marketing\Lead\Sla\LeadSlaInterface;
use App\Services\API\Marketing\Lead\Sla\LeadSlaColorsIndexService;
use App\Services\API\Marketing\Lead\Sla\LeadSlaDestroyService;
use App\Services\API\Marketing\Lead\Sla\LeadSlaIndexService;
use App\Services\API\Marketing\Lead\Sla\LeadSlaShowService;
use App\Services\API\Marketing\Lead\Sla\LeadSlaStoreService;
use App\Services\API\Marketing\Lead\Sla\LeadSlaUpdateService;
use App\Services\ExecutorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeadSlaController extends Controller implements LeadSlaInterface
{
    /**
     * Получение списка sla по лидам
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return ExecutorService::inject(LeadSlaIndexService::class)
            ->execute(
                page: $request->page ?? self::DEFAULT_PAGE,
                limit: $request->limit ?? self::DEFAULT_LIMIT_ITEMS,
                filters: $request->filters ?? null,
                sort: $request->sort ?? null,
                sort_type: $request->sort_type ?? null
            );
    }

    /**
     * Детальная информация SLA
     * @param $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        return ExecutorService::inject(LeadSlaShowService::class)
            ->execute(
                id: $id
            );
    }

    /**
     * Обновление Sla
     * @param $id
     * @param Request $request
     * @return JsonResponse
     */
    public function update($id, LeadSlaUpdateRequest $request): JsonResponse
    {
        $data = $request->validated();

        return ExecutorService::inject(LeadSlaUpdateService::class)
            ->execute(
                id: $id,
                data: $data
            );
    }

    /**
     * Создание Sla
     * @param Request $request
     * @return JsonResponse
     */
    public function store(LeadSlaStoreRequest $request): JsonResponse
    {
        $data = $request->validated();

        return ExecutorService::inject(LeadSlaStoreService::class)
            ->execute(
                data: $data
            );
    }

    /**
     * Удаление SLA
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        return ExecutorService::inject(LeadSlaDestroyService::class)
            ->execute(
                id: $id
            );
    }

}


