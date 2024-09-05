<?php

namespace App\Filament\Helper;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use App\Constants;

Class GoodForm
{

    public static function getFormGoodSchema(): array
    {
        return [
            TextInput::make('name')
                ->label("Nama Barang"),
            Select::make('ptype')
                ->label("Tipe Barang")
                ->options(Constants::PRODUCT_TYPE)
                ->required(true),
            Select::make('description')
                ->label("Deskripsi")
                ->options(Constants::DESCRIPTION_OPTIONS)
                ->required(true),
        ];
    }

}