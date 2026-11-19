<?php

namespace App\Filament\Resources\Pses;

use App\Filament\Resources\Pses\Pages\CreatePse;
use App\Filament\Resources\Pses\Pages\EditPse;
use App\Filament\Resources\Pses\Pages\ListPses;
use App\Models\Pse;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class PseResource extends Resource
{
    protected static ?string $model = Pse::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationLabel = 'Daftar PSE';

    protected static string|UnitEnum|null $navigationGroup = 'Manajemen PSE';

    protected static ?string $modelLabel = 'PSE';

    protected static ?string $pluralModelLabel = 'PSE';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Informasi Dasar')->schema([
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
                        ->placeholder('Contoh: majadigi.jatimprov.go.id')
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

                Section::make('Kategori & Klasifikasi')->schema([
                    Forms\Components\Select::make('risk_category')
                        ->label('Kategori Sistem Elektronik Berdasarkan Asas Risiko')
                        ->placeholder('--Pilih--')
                        ->required()
                        ->options([
                            'rendah' => 'Rendah',
                            'tinggi' => 'Tinggi',
                            'strategis' => 'Strategis',
                        ]),
                    Forms\Components\FileUpload::make('risk_category_doc')
                        ->label('Unggah Dokumen Pendukung Kategori SE')
                        ->directory('pse-documents/risk-category')
                        ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                        ->maxSize(10240)
                        ->openable()
                        ->required()
                        ->downloadable()
                        ->previewable()
                        ->helperText('Format PDF / DOC (Maks. 10MB)'),
                    Forms\Components\Select::make('data_classification')
                        ->label('Klasifikasi Data Sesuai Risiko')
                        ->placeholder('--Pilih--')
                        ->required()
                        ->options([
                            'terbuka' => 'Terbuka',
                            'terbatas' => 'Terbatas',
                            'tertutup' => 'Tertutup',
                        ]),
                    Forms\Components\FileUpload::make('data_classification_doc')
                        ->label('Unggah Dokumen Pendukung Klasifikasi Data')
                        ->directory('pse-documents/data-classification')
                        ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                        ->maxSize(10240)
                        ->openable()
                        ->required()
                        ->downloadable()
                        ->previewable()
                        ->helperText('Format PDF / DOC (Maks. 10MB)'),
                    Forms\Components\Select::make('personal_data_processed')
                        ->label('Keterangan Data Pribadi yang Diproses')
                        ->placeholder('Pilih Data Pribadi')
                        ->options([
                            'Kartu Keluarga' => 'Kartu Keluarga',
                            'KTP' => 'KTP',
                            'Lainnya' => 'Lainnya',
                            'SIM' => 'SIM',
                        ])
                        ->multiple()
                        ->searchable(),
                    Forms\Components\Select::make('server_location')
                        ->label('Lokasi Pengelolaan/Pemrosesan/Penyimpanan')
                        ->options([
                            'dalam_negeri' => 'Dalam Negeri',
                            'luar_negeri' => 'Luar Negeri',
                        ]),
                ])->columns(2),

                Section::make('Kewajiban Penyelenggara Sistem Elektronik')->schema([
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
                    Forms\Components\FileUpload::make('supporting_doc')
                        ->label('Unggah Dokumen Pendukung Pemenuhan Kewajiban Kelaikan SPBE (PDF/DOC/ZIP) (opsional)')
                        ->directory('pse-documents/supporting-docs')
                        ->acceptedFileTypes(['application/pdf', 'application/zip', 'application/x-zip-compressed', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                        ->maxSize(20480)
                        ->openable()
                        ->downloadable()
                        ->previewable()
                        ->helperText('Format PDF, DOC, atau ZIP (Maks. 20MB)'),
                ])->columns(1),

                Section::make('Status Pendaftaran PSE')->schema([
                    Forms\Components\Select::make('status')
                        ->label('Status PSE')
                        ->options([
                            'draft' => 'Draft',
                            'belum_terdaftar' => 'Belum Terdaftar',
                            'proses' => 'Sedang Diproses',
                            'terdaftar' => 'Terdaftar',
                        ])
                        ->default('draft')
                        ->required(),
                    Forms\Components\Textarea::make('notes')
                        ->label('Keterangan Tambahan')
                        ->columnSpanFull(),
                ])->columns(2),

                Section::make('Sertifikat TDPSE Komdigi')
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
                            ->directory('pse-badges')
                            ->image()
                            ->openable()
                            ->downloadable(),
                        Forms\Components\FileUpload::make('certificate_pdf')
                            ->label('Unggah Sertifikat Resmi (PDF)')
                            ->directory('pse-certificates')
                            ->acceptedFileTypes(['application/pdf'])
                            ->openable()
                            ->downloadable(),
                        Forms\Components\Placeholder::make('embed_preview')
                            ->label('Kode Embed Badge Resmi (HTML Snippet)')
                            ->columnSpanFull(),
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
                        'strategis' => 'Strategis',
                        'tinggi' => 'Tinggi',
                        'rendah' => 'Rendah',
                        default => ucfirst($state),
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'strategis' => 'danger',
                        'tinggi' => 'warning',
                        'rendah' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('risk_category_doc')
                    ->label('Dok. Kategori SE')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('primary')
                    ->formatStateUsing(fn ($state) => $state ? 'Download' : '-')
                    ->url(fn (Pse $record) => $record->risk_category_doc ? url('storage/'.$record->risk_category_doc) : null)
                    ->openUrlInNewTab()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('data_classification')
                    ->label('Klasifikasi Data')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'terbuka' => 'Terbuka',
                        'terbatas' => 'Terbatas',
                        'tertutup' => 'Tertutup',
                        default => ucfirst($state),
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'terbuka' => 'success',
                        'terbatas' => 'warning',
                        'tertutup' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('data_classification_doc')
                    ->label('Dok. Klasifikasi')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('info')
                    ->formatStateUsing(fn ($state) => $state ? 'Download' : '-')
                    ->url(fn (Pse $record) => $record->data_classification_doc ? url('storage/'.$record->data_classification_doc) : null)
                    ->openUrlInNewTab()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('supporting_doc')
                    ->label('Dok. SPBE')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('warning')
                    ->formatStateUsing(fn ($state) => $state ? 'Download' : '-')
                    ->url(fn (Pse $record) => $record->supporting_doc ? url('storage/'.$record->supporting_doc) : null)
                    ->openUrlInNewTab()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'terdaftar' => 'success',
                        'belum_terdaftar' => 'danger',
                        'proses' => 'warning',
                        'draft' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'terdaftar' => 'Terdaftar',
                        'belum_terdaftar' => 'Belum Terdaftar',
                        'proses' => 'Diproses',
                        'draft' => 'Draft',
                        default => $state,
                    })
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'terdaftar' => 'Terdaftar',
                        'belum_terdaftar' => 'Belum Terdaftar',
                        'proses' => 'Sedang Diproses',
                    ]),
                Tables\Filters\SelectFilter::make('opd')
                    ->options(fn () => Pse::query()->whereNotNull('opd')->where('opd', '!=', '')->distinct()->pluck('opd', 'opd')->toArray())
                    ->searchable(),
            ])
            ->actions([
                Action::make('blastWhatsApp')
                    ->label('Blast WA')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->url(function (Pse $record) {
                        $phone = preg_replace('/[^0-9]/', '', $record->contact_phone ?? '');
                        if (str_starts_with($phone, '0')) {
                            $phone = '62'.substr($phone, 1);
                        }

                        $certUrl = $record->certificate_pdf ? url('storage/'.$record->certificate_pdf) : '(Belum diunggah)';
                        $badgeImgUrl = $record->badge_image ? url('storage/'.$record->badge_image) : '';
                        $embedSnippet = $badgeImgUrl
                            ? '<a href="'.url('/pendataan-pse').'" target="_blank"><img src="'.$badgeImgUrl.'" alt="Badge PSE" style="height: 48px;" /></a>'
                            : '(Upload badge gambar lebih dulu)';

                        $msg = 'Halo Yth. Narahubung '.($record->contact_person ?? 'OPD').' ('.($record->opd ?? 'OPD Pemprov Jatim')."),\n\n"
                             ."Selamat! Sistem Elektronik Anda:\n"
                             .'📌 *'.$record->app_name."*\n\n"
                             ."Telah resmi diverifikasi dan diterbitkan Sertifikat Terdaftar PSE dengan Nomor TDPSE:\n"
                             .'📜 *'.($record->tdpse_number ?? '-')."*\n\n"
                             ."Silakan unduh Sertifikat PDF Resmi di link berikut:\n"
                             .'🔗 '.$certUrl."\n\n"
                             .'Berikut Kode Embed Badge Resmi yang WAJIB dipasang pada footer/situs web '.$record->app_name.":\n\n"
                             ."```html\n".$embedSnippet."\n```\n\n"
                             ."Terima kasih atas dukungannya dalam mewujudkan Kelaikan SPBE Pemprov Jatim.\n"
                             .'*Tim Admin JAP - Pemprov Jatim*';

                        return 'https://wa.me/'.$phone.'?text='.urlencode($msg);
                    })
                    ->openUrlInNewTab()
                    ->visible(fn (Pse $record) => ! empty($record->contact_phone) && (auth()->user()?->canModifyData() ?? false)),
                EditAction::make()
                    ->visible(fn () => auth()->user()?->canModifyData()),
                DeleteAction::make()
                    ->visible(fn () => auth()->user()?->canModifyData()),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->canModifyData()),
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
            'index' => ListPses::route('/'),
            'create' => CreatePse::route('/create'),
            'edit' => EditPse::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->canModifyData() ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->canModifyData() ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->canModifyData() ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->canModifyData() ?? false;
    }
}
