<?php

namespace App\Services\Ai;

use App\Models\AiApprovalRecord;
use App\Models\AiProviderSetting;
use App\Models\AiUsageLog;
use App\Models\Tenant;
use App\Support\TenantContext;
use ImranDevBd\AiHub\Facades\AIHub;
use RuntimeException;

class EdiAiAssistant
{
    public function explainError(Tenant $tenant, string $errorMessage, array $context = []): string
    {
        $this->assertAuthorized($tenant);

        return $this->prompt($tenant, 'explain_error', [
            'error' => $errorMessage,
            'context' => $context,
        ]);
    }

    public function explainEdi(Tenant $tenant, string $documentType, array $payload = []): string
    {
        $this->assertAuthorized($tenant);

        return $this->prompt($tenant, 'explain_edi', [
            'document_type' => $documentType,
            'payload' => $payload,
        ]);
    }

    public function suggestMapping(Tenant $tenant, array $sourceSample, array $targetSchema): array
    {
        $this->assertAuthorized($tenant);

        $suggestion = $this->prompt($tenant, 'suggest_mapping', [
            'source' => $sourceSample,
            'target_schema' => $targetSchema,
        ]);

        $record = AiApprovalRecord::create([
            'tenant_id' => $tenant->id,
            'resource_type' => 'mapping_profile',
            'resource_id' => 0,
            'suggestion_type' => 'mapping',
            'suggestion_payload' => ['text' => $suggestion],
            'status' => 'pending',
        ]);

        return [
            'suggestion' => $suggestion,
            'approval_record_id' => $record->id,
            'published' => false,
        ];
    }

    protected function assertAuthorized(Tenant $tenant): void
    {
        if (! $tenant->aiAuthorized()) {
            $setting = AiProviderSetting::query()->where('tenant_id', $tenant->id)->first();
            if (! $setting?->authorized) {
                throw new RuntimeException('Tenant AI usage is not authorized.');
            }
        }
    }

    protected function prompt(Tenant $tenant, string $feature, array $vars): string
    {
        $system = 'You are an EDI integration assistant. Never auto-publish mappings. Provide concise technical guidance.';

        $userPrompt = match ($feature) {
            'explain_error' => 'Explain this EDI integration error and remediation steps: '.json_encode($vars, JSON_THROW_ON_ERROR),
            'explain_edi' => 'Explain this EDI document type and payload structure: '.json_encode($vars, JSON_THROW_ON_ERROR),
            'suggest_mapping' => 'Suggest field mapping rules (do not publish). Return plain text bullet list: '.json_encode($vars, JSON_THROW_ON_ERROR),
            default => json_encode($vars, JSON_THROW_ON_ERROR),
        };

        try {
            $response = AIHub::system($system)->prompt($userPrompt)->send();
            $content = $response->content;
        } catch (\Throwable $e) {
            $content = 'AI unavailable: '.$e->getMessage();
        }

        AiUsageLog::create([
            'tenant_id' => $tenant->id,
            'feature' => $feature,
            'tokens_in' => 0,
            'tokens_out' => 0,
            'meta' => ['tenant_context' => TenantContext::getId()],
        ]);

        return $content;
    }
}
