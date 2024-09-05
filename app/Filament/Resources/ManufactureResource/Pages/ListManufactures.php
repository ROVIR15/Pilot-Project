<?php

namespace App\Filament\Resources\ManufactureResource\Pages;

use App\Filament\Resources\ManufactureResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListManufactures extends ListRecords
{
    protected static string $resource = ManufactureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            // button to manufacture settings pages
            Action::make('manufacture')
            ->label('Mulai Produksi')
            ->url(ManufactureResource::getUrl('settings'))
        ];
    }
}
