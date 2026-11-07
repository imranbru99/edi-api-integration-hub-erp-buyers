<?php

namespace App\Services\Partners;

use App\Models\TradingPartner;
use Illuminate\Support\Arr;

class TradingPartnerService
{
    public function create(array $data): TradingPartner
    {
        $data['status'] = $data['status'] ?? 'draft';
        $data['onboarding_step'] = $data['onboarding_step'] ?? 0;

        return TradingPartner::create($data);
    }

    public function advanceOnboarding(TradingPartner $partner, int $step): TradingPartner
    {
        $partner->onboarding_step = max($partner->onboarding_step, $step);
        $partner->save();

        return $partner;
    }

    public function activate(TradingPartner $partner): TradingPartner
    {
        $partner->status = 'active';
        $partner->onboarding_step = max($partner->onboarding_step, 5);
        $partner->save();

        return $partner;
    }

    public function updateSettings(TradingPartner $partner, array $settings): TradingPartner
    {
        $partner->settings = array_merge($partner->settings ?? [], $settings);
        $partner->save();

        return $partner;
    }

    public function isReadyForActivation(TradingPartner $partner): bool
    {
        return filled($partner->code)
            && filled($partner->name)
            && $partner->onboarding_step >= 3
            && Arr::get($partner->settings, 'connectivity_tested', false);
    }
}
