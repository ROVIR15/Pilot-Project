<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalesResource\Pages;
use App\Filament\Resources\SalesResource\RelationManagers;
use App\Models\Goods;
use App\Models\Sales;
use App\Models\Stock;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class SalesResource extends Resource
{
    protected static ?string $model = Sales::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public $goods_id;
    public $price;
    public static $qty;

    // mount
    public function mount(): void
    {
        $this->goods_id = 0;
        $this->price = 0;
        $this->qty = 0;
    }

    // get calculate total
    public static function calculateTotal($price, $qty): int
    {
        if ($price && $qty) {
            return $price * $qty;
        }

        return 0;
    }

    // get current qty given goods_id
    public static function getCurrentQty($goods_id): int
    {
        if ($goods_id) {
            $stock = Stock::where('goods_id', $goods_id)->sum('qty');
            if ($stock) {
                self::$qty = $stock;
                return $stock;
            }
        }

        return 0;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Select::make('goods_id')
                    ->label("Barang")
                    ->searchable()
                    ->options(
                        Goods::all()->pluck('name', 'id')
                    )
                    ->reactive()
                    ->afterStateUpdated(function(Set $set, $state){
                        $set('qty', self::getCurrentQty($state));
                    }),
                TextInput::make('price')
                    ->label("Harga")
                    ->reactive()
                    ->afterStateUpdated(function(Get $get, Set $set, ?string $old, ?string $state) {
                        $set('total', self::calculateTotal($state, $get('qty')));
                    })
                    ->numeric(),
                TextInput::make('qty')
                    ->numeric()
                    ->label("Jumlah"),
                TextInput::make('total')
                    ->label("Total")
                    ->disabled(true),
                TextInput::make('note')
                    ->label("Catatan"),
                Section::make("Informasi Pembelian")
                    ->schema([
                        TextInput::make('date')
                            ->label("Tgl. Transaksi")
                            ->type('date'),
                        Select::make('status')
                            ->label("Status Transaksi")
                            ->options([
                                'pending' => 'Pending',
                                'processed' => 'Di Proses',
                                'delivered' => 'Dikirim',
                                'cancelled' => 'Dibatalkan',
                                'completed' => 'Selesai',
                            ]),
                        TextInput::make('buyer_name')
                            ->label("Pembeli"),
                        TextInput::make('phone')
                            ->label("Telepon"),
                        TextInput::make('address')
                            ->label("Alamat"),
                    ]),
                Section::make("Informasi Pengiriman")
                    ->schema([
                        TextInput::make('shipping_date')
                            ->label("Tgl. Pengiriman")
                            ->type('date'),
                        TextInput::make('bill_of_lading')
                            ->label("No. Resi"),
                        TextInput::make('coo')
                            ->label("Certificate Of Origin"),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('id'),
                ImageColumn::make('qr_url')
                    ->label("QR"),
                TextColumn::make('sales_identifier')
                    ->label("No. Transaksi"),
                TextColumn::make('goods.name')
                    ->label("Barang"),
                TextColumn::make('buyer_name')
                    ->label("Pembeli"),
                TextColumn::make('price')
                    ->label("Harga"),
                TextColumn::make('qty')
                    ->label("Jumlah"),
                TextColumn::make('total')
                    ->label("Total"),
                TextColumn::make('date')
                    ->label("Tgl. Transaksi")
                    ->date(),
                TextColumn::make('shipping_date')
                    ->label("Tgl. Pengiriman")
                    ->date(),
                TextColumn::make('bill_of_lading')
                    ->label("No. Resi"),
                TextColumn::make('coo')
                    ->label("Certificate Of Origin"),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->url(
                    fn (Sales $record): string => "/admin/sales/view-sales-item-detail/{$record->sales_identifier}"
                ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSales::route('/'),
            'create' => Pages\CreateSales::route('/create'),
            'edit' => Pages\EditSales::route('/{record}/edit'),
            'view' => Pages\ViewSalesItemDetail::route('/view-sales-item-detail/{sales_identifier}'),
        ];
    }
}
