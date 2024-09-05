<?php

namespace App\Filament\Resources\WarehouseResource\Pages;

use App\Filament\Resources\WarehouseResource;
use Filament\Resources\Pages\Page;

class TableLogs extends Page
{
    protected static string $resource = WarehouseResource::class;

    protected static string $view = 'filament.resources.warehouse-resource.pages.table-logs';

    protected static ?string $title = 'Pengeluaran dan Pemasukan';
}
