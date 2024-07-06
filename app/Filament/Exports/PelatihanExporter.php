<?php

namespace App\Filament\Exports;

use App\Models\Pelatihan;
use App\Models\Pendaftaran;
use App\Models\Sertifikat;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class PelatihanExporter extends Exporter
{
    protected static ?string $model = Sertifikat::class;


    /**
     * @param string|null $model
     */
    public static function getColumns(): array
    {
        return [
            ExportColumn::make('user.no_induk')
                ->label('nidn'),
            ExportColumn::make('user.nama')
                ->label('nm_peserta'),
            ExportColumn::make('pelatihan.periode.tahun')
                ->label('thn'),
            ExportColumn::make('no_sertifikat')
                ->label('no_sert'),
            ExportColumn::make('tgl_sertifikat')
                ->label('tgl_sert'),
            ExportColumn::make('pelatihan.jam_pelatihan')
                ->label('jml_jam')
            ,
            ExportColumn::make('pelatihan.tgl_mulai')
                ->label('tgl_mulai'),
            ExportColumn::make('pelatihan.tgl_selesai')
                ->label('tgl_selesai'),
            ExportColumn::make('pelatihan.judul')
                ->label('nm_diklat'),

        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your pelatihan export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
