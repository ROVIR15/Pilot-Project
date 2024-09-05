<?php

namespace App\Filament\Resources\WarehouseResource\Pages;

use App\Filament\Resources\WarehouseResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListWarehouses extends ListRecords
{
    protected static string $resource = WarehouseResource::class;

    protected static ?string $title = 'Persediaan';
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('Log')
            ->url(WarehouseResource::getUrl('log'))
        ];
    }
}
