<?php

namespace App\Filament\Resources\ManufactureResource\Pages;

use App\Constants;
use App\Filament\Resources\ManufactureResource;
use App\Models\Goods;
use App\Models\GoodsLog;
use App\Models\ManufactureSetting;
use App\Models\Stock;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;

class Produksi extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = ManufactureResource::class;

    protected static string $view = 'filament.resources.manufacture-resource.pages.produksi';

    protected static ?string $title = "Mulai Produksi";

    protected ?string $heading = 'Mulai Produksi';

    protected ?string $subheading = 'Custom Page Subheading';

    // public variable 
    public $notes = "Hasil Produksi";
    public $goods_id;
    public $qty;

    public bool $status;

    public function mount(): void
    {
        $this->status = false;
        $this->goods_id = null;
        $this->qty = 0;
    }

    public function updateNotes(): void
    {
        if ($this->goods_id && $this->qty) {
            $estimation = $this->getEstimations($this->goods_id, $this->qty);

            $availableStock = Stock::where('warehouse_name', 'raw_materials')->sum('qty');
            $this->notes = sprintf("
                Penggunaan Bahan Baku : %d untuk memproduksi %d pcs dan sisa bahan baku : %d \n
                Status: %s
            ",
                $estimation,
                $this->qty,
                $availableStock,
                $this->getStatus($estimation, $availableStock)
            );
        }
    }

    public function getStatus(int $estimation, int $currentQty): string
    {
        if ($estimation > $currentQty) {
            $this->status = false;
            return 'Tidak Bisa Di Proses';
        } else {
            $this->status = true;
            return 'Bisa Di Proses';
        }
    }

    public function getEstimations(int $goods_id, int $qty): int
    {
        $estimation = 0;

        $ms = ManufactureSetting::where('goods_id', $goods_id)->first();
        if ($ms) {
            $estimation = $ms->consumption * $qty;
        }

        return $estimation;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Produksi')
                    ->description('Produk yang akan diproses')
                    ->schema([
                        Select::make('goods_id')
                            ->label("Produk")
                            ->options(
                                Goods::all()->pluck('name', 'id')
                                // fn (Get $get): Collection => Goods::whereHas('stock', fn (Builder $query) => $query->where('warehouse_name', $get('warehouse_name')))->get()->pluck('name', 'id')
                            )
                            ->searchable()
                            ->reactive()
                            ->afterStateUpdated(function ($state) {
                                $this->goods_id = $state;
                                $this->updateNotes();
                            }),
                        TextInput::make('qty')
                            ->label("Qty Produksi")
                            ->numeric()
                            ->validationMessages([
                                'numeric' => 'Qty harus berupa angka',
                            ])
                            ->required(true)
                            ->reactive()
                            ->afterStateUpdated(function ($state) {
                                $this->qty = $state;
                                $this->updateNotes();
                            }),
                    ]),
            ]);
    }

    // form actions
    public function getFormActions(): array
    {
        return [
            Action::make('save')
                ->disabled(!$this->status)
                ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                ->action('save')
        ];
    }

    // save
    public function save(): void
    {
        $filled = $this->form->getState();

        $newStock = Stock::create([
            'goods_id' => $filled['goods_id'],
            'qty' => $filled['qty'],
            'warehouse_name' => Constants::getKeyByValue(Constants::GOODS_CATEGORY['finished_goods']),
            'grade' => "A",
            'date' => Carbon::now()->format('Y-m-d'),
        ]);

        $newStock->save();

        $this->reduceRawMaterial($filled['qty']);

        Notification::make()
            ->title('Produksi Berhasil')
            ->success()
            ->send();
    }

    // reduce raw material
    public function reduceRawMaterial(int $reduction): void
    {
        $initial = $reduction;
        while ($initial > 0) {
            $stock = Stock::where('warehouse_name', 'raw_materials')->where('qty', '>', 0)->orderBy('id', 'asc')->first();
            if ($stock->qty < $initial) {
                $initial -= $stock->qty;
                $stock->decrement('qty', $stock->qty);
                // log movement
                $this->log(
                    Carbon::now()->format('Y-m-d'),
                    $stock->id,
                    $stock->goods_id,
                    $stock->farmer_name,
                    $stock->qty,
                    "out",
                );
            } elseif ($stock->qty >= $initial) {
                $stock->decrement('qty', $initial);
                // log
                $this->log(
                    Carbon::now()->format('Y-m-d'),
                    $stock->id,
                    $stock->goods_id,
                    $stock->farmer_name,
                    $initial,
                    "out",
                );
                $initial = 0;
            } else {
                $initial = 0;
            }
        }
    }

    public function log(
        string $_date,
        int $stock_id,
        int $goods_id,
        string $farmer_name,
        int $qty,
        string $type_movement,
    ): void {
        // dd($_date, $stock_id, $goods_id, $farmer_name, $qty, $type_movement);
        $created = GoodsLog::create([
            "date" => $_date,
            "stock_id" => $stock_id,
            "goods_id" => $goods_id,
            "farmer_name" => $farmer_name,
            "qty" => $qty,
            "type_movement" => $type_movement,
            "warehouse_name" => "raw_materials",
        ]);

        $created->save();
    }
}
