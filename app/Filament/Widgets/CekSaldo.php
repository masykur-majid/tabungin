<?php

namespace App\Filament\Widgets;

use App\Models\Account;
use App\Models\Customer;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Widgets\Widget;

class CekSaldoWidget extends Widget implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.widgets.cek-saldo';

    public ?array $data = [];
    public $account = null;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Select::make('customer_id')
                    ->label('Cari Nasabah')
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $search) => 
                        Customer::where('nama', 'like', "%{$search}%")
                            ->limit(50)
                            ->pluck('nama', 'id')
                    )
                    ->live()
                    ->afterStateUpdated(function ($state) {
                        if ($state) {
                            $this->account = Account::where('customer_id', $state)->first();
                        } else {
                            $this->account = null;
                        }

                        // Ngirim event ka widget SaldoOverview supados saldona ganti
                        $this->dispatch('update-selected-account', accountId: $this->account?->id);
                    }),
            ])
            ->statePath('data');
    }
}