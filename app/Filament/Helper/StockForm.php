<?php

namespace App\Filament\Helper;

use App\Constants;
use App\Models\Stock;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;

class StockForm
{
    public static function getFormStockSchema(bool $isStockInfo = true, string $warehouseType): array
    {
        // Validate if the warehouse type is finished goods or raw materials
        // if neither then throw an error
        Constants::validateGoodsCategory($warehouseType);

        return [
            TextInput::make('goods.name')
                ->label("Nama Barang")
                ->formatStateUsing(fn (Stock $record) => $record->goods['name'] ?? null)
                ->disabled($isStockInfo),
            Select::make('ptype')
                ->label("Tipe Barang")
                ->formatStateUsing(
                    fn (Stock $record) => $record->goods['ptype'] ?? null
                )
                ->options([
                    'KM' => 'KM',
                    'KF' => 'KF',
                    'KS' => 'KS',
                    'KA' => 'KA'
                ])
                ->disabled($isStockInfo)
                ->required(true),
            TextInput::make('farmer_name')
                ->label("Nama Pemasok")
                ->required(true),
            Select::make('goods_category')
                ->label("Kategori Barang")
                ->formatStateUsing(
                    fn (Stock $record) => $record->warehouse_name ?? null
                )
                ->disabled($isStockInfo)
                ->options(Constants::GOODS_CATEGORY),
            TextInput::make('qty')
                ->label('Quantity')
                ->numeric()
                ->disabled()
                ->minValue(0),
            Select::make('description')
                ->label('Deskripsi')
                ->formatStateUsing(
                    fn (Stock $record) => $record->goods['description'] ?? null
                )
                ->options(Constants::DESCRIPTION_OPTIONS)
                ->disabled($isStockInfo)
                ->required(true),
            Fieldset::make('additional')
                ->label('Pengukuran')
                ->schema([
                    TextInput::make('weight')
                        ->label('Berat (kg)')
                        ->numeric(),
                    TextInput::make('humidity')
                        ->label('Kelembapan (%)')
                        ->numeric(),
                    TextInput::make('thickness')
                        ->label('Tebal (mm)')
                        ->numeric(),
                ]),
        ];
    }
}