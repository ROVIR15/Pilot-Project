<?php

namespace App\Filament\Resources\GoodsResource\Pages;

use App\Filament\Helper\GoodForm;
use App\Filament\Resources\GoodsResource;
use App\Constants;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class EditStock extends EditRecord
{
    protected static string $resource = GoodsResource::class;
    
    public function form(Form $form): Form
    {
        return $form
            ->schema(GoodForm::getFormGoodSchema());
    }
}
