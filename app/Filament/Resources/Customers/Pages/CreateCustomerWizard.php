<?php

namespace App\Filament\Resources\Customers\Pages;

use App\Filament\Resources\Customers\CustomerResource;
use App\Models\Customer;
use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Callout;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;

class CreateCustomerWizard extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = CustomerResource::class;

    protected string $view = 'filament.resources.customers.pages.create-customer-wizard';

    public ?array $data = [];

    public ?string $searchStatus = null;
    
    public ?string $searchCustomerName = null;

    public ?string $searchCustomerNISN = null;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                    Step::make('Data Nasabah')
                        ->description('Pengisian Data Nasabah')
                        ->schema([
                            
                            TextInput::make('customer_id')
                                ->readOnly()
                                ->hidden()
                                ->dehydrated(),
                            
                            Callout::make('Cek Data Nasabah')
                                ->description('Masukkan NISN dan klik tombol cari untuk mencari data nasabah yang telah tersimpan.')
                                ->info()
                                ->visible(fn() => empty($this->searchStatus)),
                            
                            Callout::make('Cek Kembali NISN')
                                ->heading(fn() => match ($this->searchStatus){
                                    'empty' => 'Periksa kembali NISN',
                                    'length' => 'Periksa kembali NISN',
                                    'not_found' => 'NISN tidak ditemukan! Apakah Nasabah Baru?',
                                    'found' => "Data Nasabah Ditemukan",
                                    default => ''
                                })
                                ->description(fn() => match($this->searchStatus){
                                    'empty' => 'Masukan NISN untuk mencari data nasabah',
                                    'length' => 'Pastikan panjang NISN adalah 10 angka',
                                    'not_found' => 'Jika nasabah baru, silakan lanjutkan pengisian data nasabah baru',
                                    'found' => "Ditemukan data nasabah dengan NISN  $this->searchCustomerNISN",
                                    default => ''
                                })
                                ->color(fn() => match ($this->searchStatus){
                                    'empty' => 'danger',
                                    'length' => 'warning',
                                    'not_found' => 'info',
                                    'found' => 'success',
                                    default => ''
                                })
                                ->icon(fn() => match($this->searchStatus){
                                    'empty' => Heroicon::XCircle,
                                    'length' => Heroicon::ExclamationTriangle,
                                    'not_found' => Heroicon::InformationCircle,
                                    'found' => Heroicon::CheckCircle,
                                    default => ''
                                })
                                ->visible(fn() => !empty($this->searchStatus)),

                            TextInput::make('NISN')
                                ->label('NISN')
                                ->prefixIcon(Heroicon::Identification)
                                ->required()
                                ->mask('9999999999')
                                ->inputMode('numeric') 
                                ->length(10)
                                ->extraAttributes([
                                    'class' => 'input-wrapper-amber',
                                ])
                                ->live()
                                ->afterStateUpdated(function (Get $get, Set $set){
                                    $set('customer_id', null);
                                    $set('nama', null);
                                    $set('jenis_kelamin', null);
                                    $set('asal_sekolah', null);
                                    $set('alamat_rumah', null);
                                    $set('no_telepon', null);
                                })
                                ->suffixAction(
                                    Action::make('find_customer')
                                        ->label('Cek Nasabah')
                                        ->button()
                                        ->icon(Heroicon::MagnifyingGlass)
                                        ->action(function (Get $get, Set $set){
                                            $this->resetValidation();
                                            $nisn = $get('NISN');

                                            if(empty($nisn)){
                                                $this->searchStatus = 'empty';
                                                return ;
                                            }elseif(strlen($nisn) < 10 || strlen($nisn) > 10){
                                                $this->searchStatus = 'length';
                                                return ;
                                            }

                                            $customer = Customer::where('NISN', '=', $nisn)->first();

                                            if($customer){
                                                $set('customer_id', $customer->id);
                                                $set('nama', $customer->nama);
                                                $set('jenis_kelamin', $customer->jenis_kelamin);
                                                $set('asal_sekolah', $customer->asal_sekolah);
                                                $set('alamat_rumah', $customer->alamat_rumah);
                                                $set('no_telepon', $customer->no_telepon);

                                                $this->searchStatus = 'found';
                                                $this->searchCustomerName = $customer->nama;
                                                $this->searchCustomerNISN = $nisn;

                                            }else{
                                                $this->searchStatus = 'not_found';
                                            }
                                        })
                                ),

                            TextInput::make('nama')
                                ->prefixIcon('heroicon-m-user')
                                ->required(),


                            Radio::make('jenis_kelamin')
                                ->options(['Laki-laki' => 'Laki laki', 'Perempuan' => 'Perempuan'])
                                ->inline()
                                ->required(),
                            

                            ToggleButtons::make('asal_sekolah')
                                ->options([
                                    'SMKS 1 PARAHYANGAN' => 'SMKS 1 Parahyangan',
                                    'SMP PARAHYANGAN' => 'SMP Parahyangan',
                                ])
                                ->inline()
                                ->colors([
                                    'SMK 1 PARAHYANGAN' => 'warning',
                                    'SMP PARAHYANGAN' => 'info'
                                ])
                                ->required(),

                            TextInput::make('alamat_rumah')
                                ->prefixIcon('heroicon-m-home')
                                ->required(),

                            TextInput::make('no_telepon')
                                ->prefixIcon('heroicon-m-phone')
                                ->tel()
                                ->required()
                                ->rules(['digits_between:10,13'])
                                ->validationMessages([
                                    'digits_between' => 'Nomor telepon harus 10-13 digit.',
                                    'required' => 'Nomor telepon wajib diisi.',
                                ]),
                        ]),
                    Step::make('Rekening Nasabah')
                    ->description('Pengisian data rekening nasabah')
                    ->schema([
                        TextInput::make('nomor_rekening')
                            ->label('Nomor rekening')
                            ->required()
                            ->length(15)
                            ->unique(ignoreRecord: true)
                            ->validationMessages([
                                'required' => 'Nomor rekening wajib diisi.',
                                'length' => 'Nomor rekening harus 15 digit.',
                            ]),
                        TextInput::make('saldo')
                            ->required()
                            ->prefix('Rp')
                            ->rules(['min:0'])
                            ->numeric()
                            ->validationMessages([
                                'min' => 'Saldo tidak boleh kurang dari 0.',
                            ]),
                        Toggle::make('is_active')
                            ->default(true)
                            ->onIcon(Heroicon::Check)
                            ->offIcon(Heroicon::XMark)
                            ->onColor('success')
                            ->offColor('danger')
                            ->required(),
                    ])
                ])
                ->nextAction(
                    fn (Action $action) => $action
                        ->label('Selanjutnya')
                        ->icon(Heroicon::ArrowRight) // Ikon di sebelah kanan teks Next
                )
                ->previousAction(
                    fn (Action $action) => $action
                        ->label('Sebelumnya')
                        ->icon(Heroicon::ArrowLeft) // Ikon di sebelah kiri teks Back
                )
                ->submitAction(
                    Action::make('Simpan')
                        ->label('Simpan data')
                        ->icon(Heroicon::PencilSquare)
                        ->action('create')
                ),
            ])
            ->statePath('data');
    }

    public function create(){
        $formData = $this->form->getState();
        try {
            DB::transaction(function() use ($formData){
                if(!empty($formData['customer_id'])){
                    $customer = Customer::findOrFail($formData['customer_id']);

                    $customer->update([
                        'NISN' => $formData['NISN'],
                        'nama' => $formData['nama'],
                        'jenis_kelamin' => $formData['jenis_kelamin'],
                        'asal_sekolah' => $formData['asal_sekolah'],
                        'alamat_rumah' => $formData['alamat_rumah'],
                        'no_telepon' => $formData['no_telepon'],
                    ]);
                } 
                else{
                    $customer = Customer::create([
                        'NISN' => $formData['NISN'],
                        'nama' => $formData['nama'],
                        'jenis_kelamin' => $formData['jenis_kelamin'],
                        'asal_sekolah' => $formData['asal_sekolah'],
                        'alamat_rumah' => $formData['alamat_rumah'],
                        'no_telepon' => $formData['no_telepon'],
                    ]);
                }
                //tambahkan data rekening ke tabel rekening untuk cutomer yang diinput
                $customer->accounts()->create([
                    'nomor_rekening' => $formData['nomor_rekening'],
                    'saldo' => $formData['saldo'],
                    'is_active' => $formData['is_active'],
                ]);
            });

            Notification::make()
                ->title('Nasabah Baru Berhasil Disimpan')
                ->body('Data nasabah a.n.'.$formData['nama'].' berhasil disimpan')
                ->success()
                ->send();
            
            return redirect()->to(CustomerResource::getUrl('index'));

        } catch (Exception $e) {
            Notification::make()
                ->title('Gagal Menyimpan Data')
                ->body('Terjadi kesalahan pada sistem. Silakan coba beberapa saat lagi.')
                ->body('Error: ' . $e->getMessage()) // Aktifkan line ini jika ingin melihat detail pesan error saat debugging
                ->danger()
                ->persistent() 
                ->send();

            return null;
        }
        
    }
}
