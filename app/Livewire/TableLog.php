<?php

namespace App\Livewire;

use App\Models\Goods;
use App\Models\GoodsLog;
use App\Models\Stock;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;

class TableLog extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public static function table(Table $table): Table
    {
        return $table
            ->query(GoodsLog::query())
            ->columns([
                TextColumn::make('date'),
                TextColumn::make('stock.goods.name'),
                TextColumn::make('farmer_name'),
                TextColumn::make('warehouse_name')
                ->formatStateUsing(
                    fn ($record) => $record->warehouse_name === 'finished_goods' ? 'Gudang Barang Jadi' : 'Gudang Bahan Mentah'
                ),
                TextColumn::make('type_movement')
                ->formatStateUsing(
                    fn ($record) => $record->type_movement === 'in' ? 'Pemasukan' : 'Pengeluaran'
                ),
                TextColumn::make('qty'),
            ]);
    }

    public function render()
    {
        return view('livewire.table-log');
    }
}
