<?php

namespace App\Livewire;

use App\Models\ItemConsumption;
use App\Models\RecordProduction;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Livewire\Component;

class TableItemConsumption extends Component implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    public $rd_id;

    public function mount(int $rd_id): void
    {
        $this->rd_id = $rd_id;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(    
                self::getEloquentQuery()
            )
            ->columns([
                TextColumn::make('goods.name')
                ->label('Bahan Baku'),
                TextColumn::make('stock.farmer_name')
                ->label('Supplier'),
                TextColumn::make('consumed_qty')
                ->label('Kuantitas'),
            ]);
    }

    public function getEloquentQuery(): Builder
    {
        return ItemConsumption::query()->with('goods', 'stock')->where('record_production_id', $this->rd_id);
    }

    public function render()
    {
        return view('livewire.table-item-consumption');
    }
}
