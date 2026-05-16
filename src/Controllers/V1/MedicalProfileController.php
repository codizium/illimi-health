<?php

namespace Illimi\Health\Controllers\V1;

use Codizium\Core\Controllers\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illimi\Health\Requests\StoreMedicalProfileRequest;
use Illimi\Health\Resources\MedicalProfileResource;
use Illimi\Health\Services\MedicalProfileService;

class MedicalProfileController extends BaseController
{
    public function __construct(protected MedicalProfileService $profiles)
    {
        parent::__construct();
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);
        $perPage = max(1, min(100, $perPage));

        $records = $this->profiles->paginate(
            $request->only(['student_id']),
            $perPage
        );

        return $this->response->success(
            MedicalProfileResource::collection($records),
            'Medical profiles retrieved successfully.'
        );
    }

    public function show(string $studentId): JsonResponse
    {
        $profile = $this->profiles->getByStudent($studentId);

        if (!$profile) {
            return $this->response->error('Medical profile not found.', 404);
        }

        return $this->response->success(new MedicalProfileResource($profile), 'Medical profile retrieved successfully.');
    }

    public function store(StoreMedicalProfileRequest $request, string $studentId): JsonResponse
    {
        $profile = $this->profiles->upsert($studentId, $request->validated());

        return $this->response->success(new MedicalProfileResource($profile), 'Medical profile saved successfully.');
    }
}
