<?php

namespace App\Filament\Resources\GoodsResource\Pages;

use App\Filament\Resources\GoodsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGoods extends ListRecords
{
    protected static string $resource = GoodsResource::class;

    protected static ?string $title = "List Barang";

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->icon('heroicon-o-plus')
            ->label('Tambah Bahan Baku'),
        ];
    }
}
