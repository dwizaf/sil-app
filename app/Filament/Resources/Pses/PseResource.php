<?php

namespace App\Filament\Resources\Pses;

use App\Filament\Resources\Pses\PseResource\Pages;
use App\Models\Pse;
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class PseResource extends Resource
{
    protected static ?string $model = Pse::class;

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-o-shield-check';
    
    protected static ?string $navigationLabel = 'Daftar PSE';
    protected static string | UnitEnum | null $navigationGroup = 'Manajemen PSE';
    protected static ?string $modelLabel = 'PSE';
    protected static ?string $pluralModelLabel = 'PSE';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                \Filament\Schemas\Components\Section::make('Informasi Dasar')->schema([
                    Forms\Components\TextInput::make('instansi')
                        ->label('Instansi')
                        ->placeholder('Contoh: Pemerintah Provinsi Jawa Timur')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('opd')
                        ->label('Unit Kerja pemilik Sistem Elektronik')
                        ->placeholder('Contoh: Direktorat Jenderal Aplikasi Informatika')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('app_name')
                        ->label('Nama Sistem Elektronik')
                        ->placeholder('Contoh: Sistem Informasi Pelayanan Publik')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('version')
                        ->label('Versi Sistem Elektronik')
                        ->placeholder('Contoh: 2.5.1')
                        ->maxLength(255),
                    Forms\Components\Select::make('sector')
                        ->label('Bidang/Sektor Sistem Elektronik')
                        ->options([
                            'publik' => 'Sektor Publik',
                            'privat' => 'Sektor Privat',
                        ])
                        ->default('publik')
                        ->required(),
                    Forms\Components\TextInput::make('contact_person')
                        ->label('Narahubung Sistem Elektronik')
                        ->placeholder('Contoh: Budi Santoso')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('contact_phone')
                        ->label('No. Telp./Hp. Narahubung')
                        ->placeholder('Contoh: 081234567890')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('url')
                        ->label('Uniform Resource Locator (URL) Situs Web')
                        ->placeholder('Contoh: https://layanan.go.id')
                        ->url()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('dns_or_ip')
                        ->label('Sistem Nama Domain (DNS)/Alamat Internet IP Server')
                        ->placeholder('Contoh: 192.168.1.100 atau layanan.go.id')
                        ->maxLength(255),
                    Forms\Components\Textarea::make('description')
                        ->label('Deskripsi Singkat Fungsi dan Proses Bisnis Sistem Elektronik')
                        ->placeholder('Contoh: Sistem ini digunakan untuk mengelola layanan publik...')
                        ->columnSpanFull(),
                ])->columns(2),

                \Filament\Schemas\Components\Section::make('Kategori & Klasifikasi')->schema([
                    Forms\Components\Select::make('risk_category')
                        ->label('Kategori Sistem Elektronik Berdasarkan Asas Risiko')
                        ->options([
                            'tinggi' => 'Risiko Tinggi',
                            'menengah' => 'Risiko Menengah',
                            'rendah' => 'Risiko Rendah',
                        ]),
                    Forms\Components\Select::make('data_classification')
                        ->label('Klasifikasi Data Sesuai Risiko')
                        ->options([
                            'sangat_rahasia' => 'Sangat Rahasia',
                            'rahasia' => 'Rahasia',
                            'terbatas' => 'Terbatas',
                            'biasa' => 'Biasa/Publik',
                        ]),
                    Forms\Components\TextInput::make('personal_data_processed')
                        ->label('Keterangan Data Pribadi yang Diproses')
                        ->placeholder('Pilih Data Pribadi')
                        ->maxLength(255),
                    Forms\Components\Select::make('server_location')
                        ->label('Lokasi Pengelolaan/Pemrosesan/Penyimpanan')
                        ->options([
                            'dalam_negeri' => 'Dalam Negeri',
                            'luar_negeri' => 'Luar Negeri',
                        ]),
                ])->columns(2),

                \Filament\Schemas\Components\Section::make('Kewajiban Penyelenggara Sistem Elektronik')->schema([
                    Forms\Components\Checkbox::make('req_security')
                        ->label('Pemenuhan kewajiban untuk memastikan keamanan informasi sesuai dengan ketentuan peraturan perundang-undangan;'),
                    Forms\Components\Checkbox::make('req_protection')
                        ->label('Pemenuhan kewajiban untuk menyediakan sistem pengamanan yang mencakup prosedur dan sistem pencegahan dan penanggulangan terhadap ancaman dan serangan...'),
                    Forms\Components\Checkbox::make('req_personal_data')
                        ->label('Pemenuhan kewajiban melakukan pelindungan Data Pribadi sesuai dengan ketentuan peraturan perundang-undangan;'),
                    Forms\Components\Checkbox::make('req_feasibility')
                        ->label('Pemenuhan kewajiban melakukan uji kelaikan Sistem Elektronik sesuai dengan ketentuan peraturan perundang-undangan;'),
                    Forms\Components\Checkbox::make('req_spbe')
                        ->label('Pemenuhan ketentuan arsitektur sistem pemerintahan berbasis elektronik nasional dan arsitektur sistem pemerintahan berbasis elektronik Instansi Pusat...'),
                ])->columns(1),

                \Filament\Schemas\Components\Section::make('Status Pendaftaran PSE')->schema([
                    Forms\Components\Select::make('status')
                        ->label('Status PSE')
                        ->options([
                            'belum_terdaftar' => 'Belum Terdaftar',
                            'proses' => 'Sedang Diproses',
                            'terdaftar' => 'Terdaftar',
                        ])
                        ->default('belum_terdaftar')
                        ->required(),
                    Forms\Components\Textarea::make('notes')
                        ->label('Keterangan Tambahan')
                        ->columnSpanFull(),
                ])->columns(2),

                \Filament\Schemas\Components\Section::make('Sertifikat TDPSE Komdigi')
                    ->description('Bagian ini diisi apabila PSE sudah berstatus Terdaftar dan sertifikat telah terbit.')
                    ->schema([
                        Forms\Components\TextInput::make('tdpse_number')
                            ->label('Nomor TDPSE')
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('registration_date')
                            ->label('Tanggal Terdaftar'),
                        Forms\Components\TextInput::make('badge_link')
                            ->label('Tautan Badge Komdigi (Link)')
                            ->url()
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('badge_image')
                            ->label('Unggah Gambar Badge (PNG)')
                            ->directory('pse-certificates')
                            ->image(),
                        Forms\Components\FileUpload::make('certificate_pdf')
                            ->label('Unggah Sertifikat (PDF)')
                            ->directory('pse-certificates')
                            ->acceptedFileTypes(['application/pdf']),
                        Forms\Components\Textarea::make('badge_embed_code')
                            ->label('Kode Embed Badge HTML (Untuk Pengembang/Developer OPD)')
                            ->helperText('Salin kode HTML di bawah ini dan tempelkan pada footer atau bagian utama situs web OPD Anda.')
                            ->rows(3)
                            ->columnSpanFull()
                            ->readOnly()
                            ->formatStateUsing(function ($record) {
                                if (!$record || !$record->badge_image) {
                                    return 'Unggah gambar badge terlebih dahulu untuk menghasikan kode embed.';
                                }
                                $imageUrl = url('storage/' . $record->badge_image);
                                return '<a href="' . url('/pendataan-pse') . '" target="_blank" title="Terdaftar PSE Pemprov Jatim">' . "\n"
                                    . '  <img src="' . $imageUrl . '" alt="Badge Terdaftar PSE ' . e($record->app_name) . '" style="height: 48px;" />' . "\n"
                                    . '</a>';
                            }),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('app_name')
                    ->label('Nama Aplikasi')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('opd')
                    ->label('OPD')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('contact_person')
                    ->label('Narahubung')
                    ->searchable(),
                Tables\Columns\TextColumn::make('url')
                    ->label('URL')
                    ->searchable()
                    ->color('primary')
                    ->limit(20)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('risk_category')
                    ->label('Kategori SE')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'tinggi' => 'Risiko Tinggi',
                        'menengah' => 'Risiko Menengah',
                        'rendah' => 'Risiko Rendah',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'tinggi' => 'danger',
                        'menengah' => 'warning',
                        'rendah' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('data_classification')
                    ->label('Klasifikasi Data')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'sangat_rahasia' => 'Sangat Rahasia',
                        'rahasia' => 'Rahasia',
                        'terbatas' => 'Terbatas',
                        'biasa' => 'Biasa/Publik',
                        default => $state,
                    })
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'terdaftar' => 'success',
                        'belum_terdaftar' => 'danger',
                        'proses' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'terdaftar' => 'Terdaftar',
                        'belum_terdaftar' => 'Belum Terdaftar',
                        'proses' => 'Diproses',
                        default => $state,
                    })
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'terdaftar' => 'Terdaftar',
                        'belum_terdaftar' => 'Belum Terdaftar',
                        'proses' => 'Sedang Diproses',
                    ]),
                Tables\Filters\SelectFilter::make('opd')
                    ->options(fn () => Pse::query()->whereNotNull('opd')->where('opd', '!=', '')->distinct()->pluck('opd', 'opd')->toArray())
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\Action::make('blastWhatsApp')
                    ->label('Blast WA')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->url(function (Pse $record) {
                        $phone = preg_replace('/[^0-9]/', '', $record->contact_phone ?? '');
                        if (str_starts_with($phone, '0')) {
                            $phone = '62' . substr($phone, 1);
                        }
                        
                        $certUrl = $record->certificate_pdf ? url('storage/' . $record->certificate_pdf) : '(Belum diunggah)';
                        $badgeImgUrl = $record->badge_image ? url('storage/' . $record->badge_image) : '';
                        $embedSnippet = $badgeImgUrl 
                            ? '<a href="' . url('/pendataan-pse') . '" target="_blank"><img src="' . $badgeImgUrl . '" alt="Badge PSE" style="height: 48px;" /></a>'
                            : '(Upload badge gambar lebih dulu)';

                        $msg = "Halo Yth. Narahubung " . ($record->contact_person ?? 'OPD') . " (" . ($record->opd ?? 'OPD Pemprov Jatim') . "),\n\n"
                             . "Selamat! Sistem Elektronik Anda:\n"
                             . "📌 *" . $record->app_name . "*\n\n"
                             . "Telah resmi diverifikasi dan diterbitkan Sertifikat Terdaftar PSE dengan Nomor TDPSE:\n"
                             . "📜 *" . ($record->tdpse_number ?? '-') . "*\n\n"
                             . "Silakan unduh Sertifikat PDF Resmi di link berikut:\n"
                             . "🔗 " . $certUrl . "\n\n"
                             . "Berikut Kode Embed Badge Resmi yang WAJIB dipasang pada footer/situs web " . $record->app_name . ":\n\n"
                             . "```html\n" . $embedSnippet . "\n```\n\n"
                             . "Terima kasih atas dukungannya dalam mewujudkan Kelaikan SPBE Pemprov Jatim.\n"
                             . "*Tim Admin JAP - Pemprov Jatim*";

                        return "https://wa.me/" . $phone . "?text=" . urlencode($msg);
                    }, openInNewTab: true)
                    ->visible(fn (Pse $record) => !empty($record->contact_phone)),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\Pses\Pages\ListPses::route('/'),
            'create' => \App\Filament\Resources\Pses\Pages\CreatePse::route('/create'),
            'edit' => \App\Filament\Resources\Pses\Pages\EditPse::route('/{record}/edit'),
        ];
    }
}
