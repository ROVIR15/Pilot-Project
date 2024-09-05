<?php

namespace App\Filament\Resources\GoodsResource\Pages;

use App\Filament\Resources\GoodsResource;
use Filament\Actions;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class EditGoods extends EditRecord
{
    protected static string $resource = GoodsResource::class;

    public function form(Form $form): Form
    {
        return $form;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
