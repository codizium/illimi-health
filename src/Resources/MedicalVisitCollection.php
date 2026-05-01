<?php

namespace Illimi\Health\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class MedicalVisitCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return [
            'data' => MedicalVisitResource::collection($this->collection),
        ];
    }
}
