<?php

namespace App\Filament\Resources\GoodsResource\Pages;

use App\Filament\Resources\GoodsResource;
use App\Models\Stock;
use chillerlan\QRCode\QRCode;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CreateGoods extends CreateRecord
{
    protected static string $resource = GoodsResource::class;

    protected ?string $heading = 'Tambahkan Barang';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::user()->id;

        $data['name'] = $data['goods']['name'];

        return $data;
    }
    
    protected function afterCreate(): void
    {
        $generator = new QRCode;

        // logic generate qr
        $qrCode = $generator->render($this->record->id);
        $qrCode = str($qrCode)->replace("data:image/svg+xml;base64,", "")->toString();

        $ok = Storage::disk('public')->put('qrcodes/' . $this->record->id . '.svg', base64_decode($qrCode));
        if (!$ok) {
            throw new \Exception('Failed to save QR code');
        }

        $url = 'qrcodes/' . $this->record->id . '.svg';
        $this->record->qr_url = $url;
        $this->record->save();

        $stockData = [
            'goods_id' => $this->record->id,
            'warehouse_name' => $this->data['goods_category'],
            'date' => date('Y-m-d'), //Y-m-d,
            'farmer_name' => $this->data['farmer_name'],
            'weight' => $this->data['weight'],
            'humidity' => $this->data['humidity'],
            'thickness' => $this->data['thickness'],
            'qty' => $this->record->qty,
        ];

        Stock::create($stockData);
    }

}
