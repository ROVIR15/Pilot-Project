<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ManufactureResource\Pages;
use App\Filament\Resources\ManufactureResource\RelationManagers;
use App\Models\Goods;
use App\Models\Manufacture;
use App\Models\ManufactureSetting;
use App\Models\Stock;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ManufactureResource extends Resource
{
    protected static ?string $model = ManufactureSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Select::make('goods_id')
                    ->label("Barang Di Produksi")
                    ->options(Stock::where('warehouse_name', 'finished_goods')
                        ->where('qty', 0)
                        ->with('goods')
                        ->get()
                        ->pluck('goods.name', 'goods.id'))
                    ->searchable()
                    ->required(true),
                TextInput::make('consumption')
                    ->label("Jumlah Bahan Baku yg digunakan"),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('goods.name'),
                TextColumn::make('consumption'),
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
            'index' => Pages\ListManufactures::route('/'),
            'create' => Pages\CreateManufacture::route('/create'),
            'edit' => Pages\EditManufacture::route('/{record}/edit'),
            'settings' => Pages\Produksi::route('/produksi'),
        ];
    }
}
