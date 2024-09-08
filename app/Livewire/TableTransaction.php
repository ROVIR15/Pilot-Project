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

    public function table(Table $table): Table
    {
        return $table
            ->query(    
                self::getEloquentQuery()
            )
            ->columns([
                TextColumn::make('date'),
                TextColumn::make('status')
                ->label('Status')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'draft' => 'gray',
                    'failed' => 'danger',
                    'success' => 'success',
                }),            
                TextColumn::make('block_number')
                ->label('No. Blok'),
                TextColumn::make('transaction_hash')
                ->label('Hash')
                ->words(15),
            ]);
    }

    public function render()
    {
        return view('livewire.table-transaction');
    }

    public function getEloquentQuery(): Builder
    {
        return Transaction::query()->where('sales_id', $this->sales_id);
    }
}
