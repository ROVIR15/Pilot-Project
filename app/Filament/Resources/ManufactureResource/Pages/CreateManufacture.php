<?php

namespace App\Filament\Resources\ManufactureResource\Pages;

use App\Filament\Resources\ManufactureResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateManufacture extends CreateRecord
{
    protected static string $resource = ManufactureResource::class;

    //title
    protected static ?string $title = "Persiapan Produksi";
}
