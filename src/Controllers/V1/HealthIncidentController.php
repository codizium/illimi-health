<?php

namespace Illimi\Health\Controllers\V1;

use Codizium\Core\Controllers\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illimi\Health\Exceptions\IncidentEscalationException;
use Illimi\Health\Requests\EscalateIncidentRequest;
use Illimi\Health\Requests\StoreIncidentRequest;
use Illimi\Health\Resources\HealthIncidentResource;
use Illimi\Health\Services\IncidentService;

class HealthIncidentController extends BaseController
{
    public function __construct(protected IncidentService $incidents)
    {
        parent::__construct();
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);
        $perPage = max(1, min(100, $perPage));
        $records = $this->incidents->paginate($perPage);

        return $this->response->success(HealthIncidentResource::collection($records), 'Incidents retrieved successfully.');
    }

    public function store(StoreIncidentRequest $request): JsonResponse
    {
        $incident = $this->incidents->create($request->validated());

        return $this->response->success(new HealthIncidentResource($incident), 'Incident reported successfully.', 201);
    }

    public function escalate(EscalateIncidentRequest $request, string $id): JsonResponse
    {
        try {
            $incident = $this->incidents->escalate($id, $request->validated());
        } catch (IncidentEscalationException $exception) {
            return $this->response->error($exception->getMessage(), 422);
        }

        return $this->response->success(new HealthIncidentResource($incident), 'Incident escalated successfully.');
    }
}
