<?php

namespace App\Livewire;

use App\Models\Stock;
use App\Models\Transaction;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Livewire\Component;

class TableTransaction extends Component implements HasForms, HasTable
{
    use InteractsWithForms, InteractsWithTable;

    // mount

    public $sales_id;

    public $data;

    public function mount(int $sales_id): void
    {
        $this->sales_id = $sales_id;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(Transaction::query())
            ->columns([
                TextColumn::make('date'),
                TextColumn::make('status'),
                TextColumn::make('block_number'),
                TextColumn::make('transaction_hash'),
            ]);
    }
    
    public function render()
    {
        return view('livewire.table-transaction');
    }
}
