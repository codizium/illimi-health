<?php

namespace Illimi\Health\Controllers\V1;

use Codizium\Core\Controllers\BaseController;
use Illuminate\Http\JsonResponse;
use Illimi\Health\Requests\StoreMedicalProfileRequest;
use Illimi\Health\Resources\MedicalProfileResource;
use Illimi\Health\Services\MedicalProfileService;

class MedicalProfileController extends BaseController
{
    public function __construct(protected MedicalProfileService $profiles)
    {
        parent::__construct();
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
