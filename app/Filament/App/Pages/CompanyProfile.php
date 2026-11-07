<?php

namespace App\Filament\App\Pages;

use App\Models\Tenant;
use App\Support\TenantContext;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class CompanyProfile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';

    protected static string $view = 'filament.app.pages.company-profile';

    protected static ?string $navigationLabel = 'Company Profile';

    protected static ?int $navigationSort = 1;

    public ?array $data = [];

    public function mount(): void
    {
        $tenant = $this->resolveTenant();

        if ($tenant) {
            $this->form->fill($tenant->only([
                'name',
                'legal_name',
                'email',
                'phone',
                'website',
                'country',
                'timezone',
                'currency',
            ]));
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required(),
                Forms\Components\TextInput::make('legal_name'),
                Forms\Components\TextInput::make('email')->email(),
                Forms\Components\TextInput::make('phone')->tel(),
                Forms\Components\TextInput::make('website')->url(),
                Forms\Components\TextInput::make('country')->maxLength(2),
                Forms\Components\TextInput::make('timezone'),
                Forms\Components\TextInput::make('currency')->maxLength(3),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $tenant = $this->resolveTenant();

        if ($tenant === null) {
            return;
        }

        $tenant->update($this->form->getState());

        Notification::make()
            ->title('Company profile saved')
            ->success()
            ->send();
    }

    protected function resolveTenant(): ?Tenant
    {
        return TenantContext::get() ?? auth()->user()?->currentTenant;
    }
}
