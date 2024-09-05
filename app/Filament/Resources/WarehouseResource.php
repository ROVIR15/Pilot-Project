<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WarehouseResource\Pages;
use App\Filament\Resources\WarehouseResource\Pages\TableLogs;
use App\Models\Goods;
use App\Models\Stock;
use App\Models\Warehouse;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\DB;

class WarehouseResource extends Resource
{
    protected static ?string $model = Stock::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Persediaan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Select::make('warehouse_name')
                    ->label("Gudang")
                    ->options([
                        'finished_goods' => 'Gudang Barang Jadi',
                        'raw_materials' => 'Gudang Bahan Mentah',
                    ]),
                Select::make('goods_id')
                    ->label("Nama Barang")
                    ->options(
                        fn(Get $get): SupportCollection => Goods::whereHas('stock', fn(Builder $query) => $query->where('warehouse_name', $get('warehouse_name')))->get()->pluck('name', 'id')
                    )
                    ->searchable(),
                TextInput::make('qty'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('goods.name')
                    ->label("Nama Barang"),
                TextColumn::make('goods.ptype')
                    ->label("Jenis Barang"),
                TextColumn::make('qty'),
                TextColumn::make('warehouse_name')
                    ->label("Gudang")
                    // replace the value with the name of the warehouse
                    ->getStateUsing(function (Stock $record) {
                        switch ($record->warehouse_name) {
                            case 'finished_goods':
                                return 'Gudang Barang Jadi';
                            case 'raw_materials':
                                return 'Gudang Bahan Mentah';
                        }
                    }),

            ])
            ->filters([
                SelectFilter::make('warehouse_name')
                    ->options([
                        'finished_goods' => 'Gudang Barang Jadi',
                        'raw_materials' => 'Gudang Bahan Mentah',
                    ])
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->select('goods_id', 'warehouse_name', 'farmer_name', DB::raw('sum(qty) as qty, min(id) as id, max(weight) as weight, max(humidity) as humidity, max(thickness) as thickness'))
            ->with('goods')
            ->groupBy('goods_id', 'warehouse_name', 'farmer_name')
            ->orderBy('id', 'desc');

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWarehouses::route('/'),
            'create' => Pages\CreateWarehouse::route('/create'),
            'edit' => Pages\EditWarehouse::route('/{record}/edit'),
            'log' => TableLogs::route('/log'),
        ];
    }
}
