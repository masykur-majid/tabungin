<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(fn ($livewire): string => match (true) {
                    $livewire instanceof CreateRecord => 'Tambahkan Pengguna',
                    $livewire instanceof EditRecord => 'Edit Pengguna',
                    default => 'Post Details',
                })
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('email')
                            ->label('Email address')
                            ->email()
                            ->prefixIcon(Heroicon::AtSymbol)
                            ->required(),
                        // DateTimePicker::make('email_verified_at'),
                        TextInput::make('password')
                            ->password()
                            ->revealable()
                            ->required(fn ($livewire) => $livewire instanceof CreateRecord)
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                            ->helperText(fn ($livewire) => $livewire instanceof CreateRecord 
                                ? null 
                                : 'Leave blank if you do not want to change the password.'),
                        Select::make('roles')
                            ->relationship('roles', 'name')
                            ->required(),
                        Actions::make([
                            Action::make('submit')
                                ->label('Simpan')
                                ->color('primary')
                                ->icon(Heroicon::PencilSquare)
                                ->submit('save'), 
                            Action::make('delete')
                                ->label('Delete')
                                ->icon(Heroicon::Trash)
                                ->color('danger')
                                ->requiresConfirmation()
                                ->action(fn ($livewire) => $livewire->delete())
                                ->visible(fn ($livewire) => $livewire instanceof EditRecord),
                            Action::make('cancel')
                                ->label('Batalkan')
                                ->color('gray')
                                ->url(UserResource::getUrl('index')), // Navigates back to resource list
                        ])
                        ->alignment('right'),
                    ])
            ])
            ->columns(1);
    }
}
