<?php

namespace App\Filament\Resources\ManufactureResource\Pages;

use App\Filament\Resources\ManufactureResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditManufacture extends EditRecord
{
    protected static string $resource = ManufactureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
