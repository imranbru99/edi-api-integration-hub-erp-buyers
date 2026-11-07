<?php

namespace App\Filament\App\Pages;

use App\Models\Module;
use App\Models\TenantModule;
use App\Services\Modules\ModuleRegistryService;
use App\Support\TenantContext;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ModuleSettings extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-cog-8-tooth';

    protected static string $view = 'filament.app.pages.module-settings';

    protected static ?string $navigationLabel = 'Module Settings';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Module::query()->where('is_available', true)->orderBy('sort_order'))
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('code'),
                Tables\Columns\IconColumn::make('is_core')->boolean(),
                Tables\Columns\IconColumn::make('tenant_active')
                    ->label('Active')
                    ->boolean()
                    ->getStateUsing(fn (Module $record) => $this->isModuleActive($record)),
            ])
            ->actions([
                Tables\Actions\Action::make('activate')
                    ->visible(fn (Module $record) => ! $this->isModuleActive($record))
                    ->action(function (Module $record): void {
                        $tenant = TenantContext::get();
                        if ($tenant) {
                            app(ModuleRegistryService::class)->activate($tenant, $record->code);
                            Notification::make()->title('Module activated')->success()->send();
                        }
                    }),
                Tables\Actions\Action::make('deactivate')
                    ->color('danger')
                    ->visible(fn (Module $record) => $this->isModuleActive($record) && ! $record->is_core)
                    ->action(function (Module $record): void {
                        $tenant = TenantContext::get();
                        if ($tenant) {
                            app(ModuleRegistryService::class)->deactivate($tenant, $record->code);
                            Notification::make()->title('Module deactivated')->success()->send();
                        }
                    }),
            ]);
    }

    protected function isModuleActive(Module $record): bool
    {
        $tenantId = TenantContext::getId() ?? auth()->user()?->current_tenant_id;

        if ($tenantId === null) {
            return false;
        }

        return TenantModule::query()
            ->withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->where('module_id', $record->id)
            ->where('is_active', true)
            ->exists();
    }
}
