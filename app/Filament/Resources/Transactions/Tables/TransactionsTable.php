<?php

namespace App\Filament\Resources\Transactions\Tables;

use App\Enums\TransactionStatus;
use App\Filament\Resources\Transactions\TransactionResource;
use App\Models\Transaction;
use App\Services\TransactionService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Size;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Support\HtmlString;

use function Laravel\Prompts\form;

class TransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->stickyableColumns()
            ->emptyStateHeading('Catatan Transaksi Masih Kosong.')
            ->emptyStateDescription('Belum ada transaksi yang tercatat di sistem.')
            ->emptyStateIcon('fas-money-bill-transfer')
            ->emptyStateActions([
                Action::make('create')
                    ->label('Catat Transaksi Baru')
                    ->icon(Heroicon::PencilSquare)
                    ->url(fn() => TransactionResource::getUrl('create'))
                    ->button()
            ])

            ->columns([
                TextColumn::make('created_at')
                    ->dateTime('d F Y, H:i:s')
                    ->sortable(),
                TextColumn::make('no_slip')
                    ->label('No. Transaksi')
                    ->searchable(),

                TextColumn::make('account.nomor_rekening')
                    ->label('No. Rekening')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('account.customer.nama')
                    ->label('Nama Nasabah')
                    ->searchable()
                    ->sortable(),
                               
                // TextColumn::make('tanggal')
                //     ->date()
                //     ->sortable(),
                TextColumn::make('jenis_transaksi')
                    ->badge()
                    ->color(fn($state) => match($state){
                        'setoran' => Color::Blue,
                        'penarikan' => Color::Rose,
                    })
                    ->searchable(),
                TextColumn::make('jumlah_transaksi')
                    ->label('Jumlah')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Petugas')
                    ->searchable()
                    ->sortable(),
               TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_printed')
                    ->boolean()
                    ->label('Dicetak?')
                    ->alignCenter()
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->hiddenLabel()
                        ->tooltip('Lihat detail')
                        ->color('normal'),
                    EditAction::make(),
                    DeleteAction::make(),

                    Action::make('Setujui Pembatalan')
                        ->modal()
                        ->modalHeading('Konfirmasi Pembatalan')
                        ->modalDescription( function ($record){
                            return new HtmlString("Apakah anda yakin akan membatalakan transaksi <strong>{$record->no_slip}</strong>");
                        })
                        ->modalIcon(Heroicon::ExclamationCircle)
                        ->requiresConfirmation()
                        ->schema([
                            Textarea::make('alasan_pembatalan')
                                ->label('Alasan Pengajuan Pembatalan:')
                                ->dehydrated(false)
                                ->default(fn($record) => $record->alasan_batal)
                                ->readOnly(),
                            TextInput::make('diajukan_batal_oleh')
                                ->label('Diajukan Oleh:')
                                ->dehydrated(false)
                                ->default(fn($record) => $record->user->name)
                                ->readOnly(),
                        ])
                        ->hidden(fn($record) => Filament::auth()->user()->hasRole('petugas') || $record->status != TransactionStatus::PendingCancellation)
                        ->icon(Heroicon::Check)
                        ->color(Color::Blue)
                        ->action(function (Transaction $transaction){
                            
                            $trx = app(TransactionService::class)->approveCancellation($transaction->id, auth()->id());
                            if($trx){
                                Notification::make()
                                ->title('Pembatalan Transaksi Berhasil!')
                                ->success()
                                ->send();
                            }else{
                                Notification::make()
                                    ->title('terjadi kesalahan sistem')
                                    ->success()
                                    ->send();
                            }

                            
                        }),
                    Action::make('Ajukan Pembatalan')
                        ->modal()
                        ->modalHeading('Yakin Ingin Membatalkan Transaksi Ini?')
                        ->modalDescription('Masukan alasan untuk mengajukan pembatalan transaksi ini')
                        ->modalIcon(Heroicon::ExclamationTriangle)
                        ->schema([
                            Textarea::make('alasan_pembatalan')
                                ->label('Alasan Pengajuan Pembatalan')
                                ->required(),
                        ])
                        ->size(Size::ExtraLarge)
                        ->requiresConfirmation()
                        ->hidden(fn($record): bool => Filament::auth()->user()->hasRole('super_admin') || Filament::auth()->user()->hasRole('supervisor') || $record->status == TransactionStatus::PendingCancellation || $record->status == TransactionStatus::Cancelled)
                        ->icon(Heroicon::XCircle)
                        ->color(Color::Red)
                        ->action(function (Transaction $transaction, array $data){
                            
                            $trx = app(TransactionService::class)->requestCancellation($transaction->id, auth()->id(), $data['alasan_pembatalan']);
                            if($trx){
                                Notification::make()
                                ->title('Pengajuan Pembatalan telah dikirim.')
                                ->success()
                                ->send();
                            }else{
                                Notification::make()
                                    ->title('terjadi kesalahan sistem')
                                    ->success()
                                    ->send();
                            }
                        }),
                    Action::make('Cetak Slip')
                        ->label('Cetak Bukti Transaksi')
                        ->icon(Heroicon::Printer)
                        ->color('info')
                        ->hidden(fn($record): bool => $record->status != 'berhasil')
                ])                
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActionsPosition(RecordActionsPosition::AfterColumns)
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
