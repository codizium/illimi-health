<?php

namespace Illimi\Health\Controllers\V1;

use Codizium\Core\Controllers\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illimi\Health\Requests\StoreImmunizationRequest;
use Illimi\Health\Resources\ImmunizationResource;
use Illimi\Health\Services\ImmunizationService;

class ImmunizationController extends BaseController
{
    public function __construct(protected ImmunizationService $immunizations)
    {
        parent::__construct();
    }

    public function all(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);
        $perPage = max(1, min(100, $perPage));

        $records = $this->immunizations->paginate(
            $request->only(['student_id', 'status', 'due_only']),
            $perPage
        );

        return $this->response->success(
            ImmunizationResource::collection($records),
            'Immunizations retrieved successfully.'
        );
    }

    public function index(string $studentId): JsonResponse
    {
        return $this->response->success(
            ImmunizationResource::collection($this->immunizations->forStudent($studentId)),
            'Immunizations retrieved successfully.'
        );
    }

    public function store(StoreImmunizationRequest $request): JsonResponse
    {
        $record = $this->immunizations->create($request->validated());

        return $this->response->success(new ImmunizationResource($record), 'Immunization recorded successfully.', 201);
    }

    public function due(): JsonResponse
    {
        return $this->response->success(
            ImmunizationResource::collection($this->immunizations->due()),
            'Due immunizations retrieved successfully.'
        );
    }
}
