<?php

namespace App\Filament\Resources\SalesResource\Pages;

use App\Filament\Resources\SalesResource;
use App\Models\Stock;
use App\Models\Transaction;
use chillerlan\QRCode\QRCode;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Storage;

class CreateSales extends CreateRecord
{
    protected static string $resource = SalesResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // create sales identifier
        $data['sales_identifier'] = 'S' . date('Ymd') . '-' . str_pad(mt_rand(1000, 9999), 4, '0', STR_PAD_LEFT);
        $data['total'] = $data['price'] * $data['qty'];
        $data['warehouse_name'] = 'finished_goods';

        $generator = new QRCode;

        // logic generate qr
        $qrCode = $generator->render($data['sales_identifier']);
        $qrCode = str($qrCode)->replace("data:image/svg+xml;base64,", "")->toString();

        $ok = Storage::disk('public')->put('qrcodes/' . $data['sales_identifier'] . '.svg', base64_decode($qrCode));
        if (!$ok) {
            throw new \Exception('Failed to save QR code');
        }

        $url = 'qrcodes/' . $data['sales_identifier'] . '.svg';

        $data['qr_url'] = $url;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    // beforeSave
    protected function beforeCreate(): void
    {
        //
    }

    // afterSave
    protected function afterCreate(): void
    {
        $client = new Client();

        $randUnix = self::getRandomUnixTimestamp();

        $url = 'https://deep-index.moralis.io/api/v2.2/dateToBlock?chain=eth&date=' . $randUnix;

        $asyncReq = new Request('GET', $url);

        $response = $client->sendAsync($asyncReq, [
            'headers' => [
                'Accept' => 'application/json',
                'X-API-Key' => 'kgN0HJGb6ftjLSNLxh8wxcH6eQDKopLbvdzXd2MXijDVPaQGECITPBvAUetxSQlE',
            ],
        ])->wait();

        $body = $response->getBody(); // This is a Stream object

        // Get the contents as a string
        $contents = $body->getContents();

        // Optionally, decode if it's JSON
        $data = json_decode($contents, true); // Now $data is an array

        $url2 = 'https://deep-index.moralis.io/api/v2.2/block/' . $data['block'] . '?chain=eth';
        $response2 = $client->request('GET', $url2, [
            'headers' => [
                'Accept' => 'application/json',
                'X-API-Key' => 'kgN0HJGb6ftjLSNLxh8wxcH6eQDKopLbvdzXd2MXijDVPaQGECITPBvAUetxSQlE',
            ],
        ]);

        $body2 = $response2->getBody(); // This is a Stream object

        // Get the contents as a string
        $contents2 = $body2->getContents();

        // Optionally, decode if it's JSON
        $data2 = json_decode($contents2, true); // Now $data is an array

        $trx = Transaction::create([
            'date' => date('Y-m-d', $randUnix),
            'status' => 'success',
            'transaction_hash' => $data2['transactions'][0]['logs'][0]['transaction_hash'],
            'block_number' => $data['block'],
            'farmer_name' => 'PT Indomarco Nusantara',
            'goods_id' => $this->record->goods_id,
            'sales_id' => $this->record->id,
        ]);

        $trx->save();
    }

    // method for create random unix timestamps
    // in range year of 2020 to 2024
    public static function getRandomUnixTimestamp(): int
    {
        $min = strtotime('-5 minutes');
        // max current time
        $max = time();

        $range = $max - $min;

        return $min + mt_rand() % $range;
    }

}
