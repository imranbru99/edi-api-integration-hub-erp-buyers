<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\MappingProfileResource;
use App\Models\MappingProfile;
use App\Models\Tenant;
use App\Services\Ai\EdiAiAssistant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MappingController extends Controller
{
    public function index(): JsonResponse
    {
        $profiles = MappingProfile::query()->with('versions')->latest()->paginate(20);

        return MappingProfileResource::collection($profiles)->response();
    }

    public function show(MappingProfile $mappingProfile): JsonResponse
    {
        $mappingProfile->load('versions.rules');

        return (new MappingProfileResource($mappingProfile))->response();
    }

    public function suggest(Request $request, EdiAiAssistant $assistant): JsonResponse
    {
        $data = $request->validate([
            'tenant_id' => ['required', 'integer', 'exists:tenants,id'],
            'source_sample' => ['required', 'array'],
            'target_schema' => ['required', 'array'],
        ]);

        $tenant = Tenant::query()->findOrFail($data['tenant_id']);
        $result = $assistant->suggestMapping($tenant, $data['source_sample'], $data['target_schema']);

        return response()->json(['data' => $result]);
    }
}
