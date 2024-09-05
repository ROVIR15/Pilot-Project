<?php

namespace App\Filament\Resources\SalesResource\Pages;

use App\Filament\Resources\SalesResource;
use App\Models\Sales;
use App\Models\Transaction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;

class ViewSalesItemDetail extends Page
{
    protected static string $resource = SalesResource::class;

    protected static string $view = 'filament.resources.sales-resource.pages.view-sales-item-detail';

    protected static ?string $title = "Transaksi";

    public $record;

    public $transaction;

    // mount
    public function mount(string $sales_identifier): void
    {
        $data = Sales::where('sales_identifier', $sales_identifier)->first();
        if ($data) {
            $this->record = $data;
        } else {
            $this->record = null;
        }
        
        $sales = Transaction::where('sales_id', $this->record->id)->get();
        if ($sales) {
            $this->transaction = $sales;
        } else {
            $this->transaction = null;
        }
    }

    // onboarding
    public function onboarding(): void
    {
        Notification::make()
            ->title("Welcome to Sales Page")
            ->success()
            ->send();
    }

}
