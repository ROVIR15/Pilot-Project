<?php

namespace App\Filament\Resources\WarehouseResource\Pages;

use App\Filament\Helper\GoodForm;
use App\Filament\Helper\StockForm;
use App\Filament\Resources\WarehouseResource;
use App\Models\Stock;
use Filament\Actions;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Database\Eloquent\Builder;

class EditWarehouse extends EditRecord
{
    protected static string $resource = WarehouseResource::class;

    public array $additionalInfo = [];

    public function form(Form $form): Form
    {
        return $form
            ->schema(StockForm::getFormStockSchema(true, $this->record->warehouse_name));   
    }
    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }
}
