<?php

namespace Illimi\Health\Controllers\V1;

use Codizium\Core\Controllers\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illimi\Health\Requests\StoreMedicalVisitRequest;
use Illimi\Health\Resources\MedicalVisitCollection;
use Illimi\Health\Resources\MedicalVisitResource;
use Illimi\Health\Services\MedicalVisitService;

class MedicalVisitController extends BaseController
{
    public function __construct(protected MedicalVisitService $visits)
    {
        parent::__construct();
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);
        $perPage = max(1, min(100, $perPage));

        $records = $this->visits->paginate($request->only(['student_id', 'visit_date']), $perPage);

        return $this->response->success(new MedicalVisitCollection($records), 'Medical visits retrieved successfully.');
    }

    public function show(string $id): JsonResponse
    {
        $visit = $this->visits->find($id);

        if (!$visit) {
            return $this->response->error('Medical visit not found.', 404);
        }

        return $this->response->success(new MedicalVisitResource($visit), 'Medical visit retrieved successfully.');
    }

    public function store(StoreMedicalVisitRequest $request): JsonResponse
    {
        $visit = $this->visits->create($request->validated());

        return $this->response->success(new MedicalVisitResource($visit), 'Medical visit logged successfully.', 201);
    }
}
