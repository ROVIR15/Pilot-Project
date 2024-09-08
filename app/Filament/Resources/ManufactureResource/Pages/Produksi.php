<?php

namespace App\Filament\Resources\ManufactureResource\Pages;

use App\Constants;
use App\Filament\Resources\ManufactureResource;
use App\Models\Goods;
use App\Models\GoodsLog;
use App\Models\ItemConsumption;
use App\Models\ManufactureSetting;
use App\Models\RecordProduction;
use App\Models\Stock;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Str;

class Produksi extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = ManufactureResource::class;

    protected static string $view = 'filament.resources.manufacture-resource.pages.produksi';

    protected static ?string $title = "Mulai Produksi";

    protected ?string $heading = 'Mulai Produksi';

    // public variable 
    public $notes = "Hasil Produksi";
    public $goods_id;
    public $qty;

    public $raw_material_items = [];

    public bool $status;

    public function mount(): void
    {
        $this->status = false;
        $this->goods_id = null;
        $this->qty = 0;
        $this->raw_material_items = [];
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
                                // Goods::whereHas('stock', fn (Builder $query) => $query->where('warehouse_name', 'raw_materials'))
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
                            ->postfix('pcs')
                            ->required(true)
                            ->reactive()
                            ->afterStateUpdated(function ($state) {
                                $this->qty = $state;
                                $this->updateNotes();
                            }),
                        Repeater::make('raw_material_items')
                            ->label("Bahan Baku yg digunakan")
                            ->schema([
                                Select::make('stock_id')
                                    ->label("Bahan Baku")
                                    ->options(
                                        Stock::selectRaw('min(id) as id, goods_id, sum(qty) as qty')
                                            ->where('warehouse_name', 'raw_materials')
                                            ->where('qty', '>', 0)
                                            ->with('goods')
                                            ->groupBy('goods_id')
                                            ->get()
                                            ->pluck('goods.name', 'id')
                                    )
                                    ->searchable(),
                                TextInput::make('consumable_qty')
                                    ->label("Qty")
                                    ->numeric()
                            ])
                            ->columns(2)
                            ->statePath('raw_material_items')

                    ])
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

        DB::beginTransaction();
        try {
            // Record Production
            $rProd = RecordProduction::create([
                'goods_id' => $filled['goods_id'],
                'date' => Carbon::now()->format('Y-m-d'),
                'identifier' => $this->getIdentifier(),
                'qty' => $filled['qty'],
            ]);

            $rProd->save();

            $newStock = Stock::create([
                'goods_id' => $filled['goods_id'],
                'qty' => $filled['qty'],
                'warehouse_name' => Constants::getKeyByValue(Constants::GOODS_CATEGORY['finished_goods']),
                'grade' => "A",
                'date' => Carbon::now()->format('Y-m-d'),
            ]);

            $newStock->save();

            if ($filled['raw_material_items'] && count($filled['raw_material_items']) > 0) {
                foreach ($filled['raw_material_items'] as $rawMaterial) {
                    $stock = Stock::where('id', $rawMaterial['stock_id'])->first();
                    // update 
                    Stock::where('id', $rawMaterial['stock_id'])->update([
                        'qty' => $stock->qty - $rawMaterial['consumable_qty'],
                    ]);

                    // log item consumption
                    ItemConsumption::create([
                        'record_production_id' => $rProd->id,
                        'identifier' => $rProd->identifier,
                        'stock_id' => $rawMaterial['stock_id'],
                        'goods_id' => $filled['goods_id'],
                        'consumed_qty' => $rawMaterial['consumable_qty'],
                        'farmer_name' => $stock->farmer_name,
                    ]);

                    $stock->save();

                    // log goods move out
                    $this->log(
                        Carbon::now()->format('Y-m-d'),
                        $rawMaterial['stock_id'],
                        $filled['goods_id'],
                        $stock->farmer_name,
                        $rawMaterial['consumable_qty'],
                        "out",
                    );
                }
            }

            // log goods move in
            $this->log(
                Carbon::now()->format('Y-m-d'),
                $newStock->id,
                $filled['goods_id'],
                "Produksi",
                $filled['qty'],
                "in",
            );

            Notification::make()
            ->title('Produksi Berhasil')
            ->success()
            ->send();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Notification::make()
                ->title('Produksi Gagal')
                ->danger()
                ->body($e->getMessage())
                ->send();
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

    public function getIdentifier(): string
    {
        return substr(str_shuffle(MD5(microtime())), 0, 10);
    }
}
