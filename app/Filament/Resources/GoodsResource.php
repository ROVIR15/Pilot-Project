<?php

namespace App\Filament\Resources;

use App\Constants;
use App\Filament\Helper\StockForm;
use App\Filament\Resources\GoodsResource\Pages;
use App\Models\Goods;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GoodsResource extends Resource
{
    protected static ?string $model = Goods::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Receiving';

    protected static ?string $title = 'Receiving';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('goods.name')
                    ->label("Nama Barang"),
                Select::make('ptype')
                    ->label("Tipe Barang")
                    ->options([
                        'KM' => 'KM',
                        'KF' => 'KF',
                        'KS' => 'KS',
                        'KA' => 'KA'
                    ])
                    ->required(true),
                TextInput::make('farmer_name')
                    ->label("Nama Pemasok")
                    ->required(true),
                Select::make('goods_category')
                    ->label("Kategori Barang")
                    ->options(Constants::GOODS_CATEGORY),
                TextInput::make('qty')
                    ->label('Quantity')
                    ->numeric()
                    ->postfix('kg')
                    ->minValue(0),
                Select::make('description')
                    ->label('Deskripsi')
                    ->options(Constants::DESCRIPTION_OPTIONS)
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('ptype'),
                TextColumn::make('description'),
                ImageColumn::make('qr_url')
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListGoods::route('/'),
            'create' => Pages\CreateGoods::route('/create'),
            'edit' => Pages\EditStock::route('/{record}/edit'),
        ];
    }
}
