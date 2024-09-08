<?php

namespace App\Filament\Resources;

use App\Constants;
use App\Filament\Resources\ManufactureResource\Pages;
use App\Filament\Resources\ManufactureResource\RelationManagers;
use App\Models\Goods;
use App\Models\Manufacture;
use App\Models\ManufactureSetting;
use App\Models\Stock;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class ManufactureResource extends Resource
{
    protected static ?string $model = ManufactureSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Select::make('goods_id')
                    ->label("Barang Di Produksi")
                    ->options(Stock::where('warehouse_name', 'finished_goods')
                        ->with('goods')
                        ->get()
                        ->pluck('goods.name', 'goods.id'))
                    ->searchable()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->required(),
                        Select::make('ptype')
                            ->label("Tipe Barang")
                            ->options([
                                'KM' => 'KM',
                                'KF' => 'KF',
                                'KS' => 'KS',
                                'KA' => 'KA'
                            ])
                            ->required(true),
                        Select::make('description')
                            ->label('Deskripsi')
                            ->options(Constants::DESCRIPTION_OPTIONS)
                            ->required(true),
                    ])
                    ->createOptionUsing(function (array $data) {
                        self::createNewGoods($data);
                    })
                    ->required(true),
                TextInput::make('consumption')
                    ->postfix('kg')
                    ->label("Jumlah Bahan Baku yg digunakan")
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('goods.name'),
                TextColumn::make('consumption'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function createNewGoods(array $data): void
    {
        DB::beginTransaction();

        try {
            $new = Goods::create([
                'name' => $data['name'],
                'ptype' => $data['ptype'],
                'description' => $data['description'],
                'qty' => 0,
                'user_id' => 1
            ]);

            $new->save();

            $stock = Stock::create([
                'goods_id' => $new->id,
                'warehouse_name' => 'finished_goods',
                'qty' => 0,
                'farmer_name' => 'Pabrikan',
            ]);

            $stock->save();

            DB::commit();

            Notification::make()
                ->title('Barang baru ditambahkan')
                ->success()
                ->send();
        } catch (\Throwable $th) {
            //throw $th;

            DB::rollBack();

            Notification::make()
                ->title($th->getMessage())
                ->danger()
                ->send();
        }
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
            'index' => Pages\ListManufactures::route('/'),
            'create' => Pages\CreateManufacture::route('/create'),
            'edit' => Pages\EditManufacture::route('/{record}/edit'),
            'settings' => Pages\Produksi::route('/produksi'),
        ];
    }
}
