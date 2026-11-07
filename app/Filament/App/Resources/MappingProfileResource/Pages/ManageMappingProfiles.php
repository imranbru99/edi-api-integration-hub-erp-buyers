<?php

namespace App\Filament\App\Resources\MappingProfileResource\Pages;

use App\Filament\App\Resources\MappingProfileResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageMappingProfiles extends ManageRecords
{
    protected static string $resource = MappingProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}