<x-filament-panels::page>
    @if($this->record)
        <div class="bg-white rounded-lg shadow">
            <div class="px-4 py-5 sm:px-6 sm:px-0">
                <h2 class="text-base font-semibold leading-7 text-gray-900">Purchase Order</h2>
                <p class="mt-1 max-w-2xl text-sm leading-6 text-gray-500">

                    {{ \Carbon\Carbon::parse($this->record->date)->format('d F Y') }}
                    <b>{{ $this->record->sales_identifier  }}</b>
                </p>
            </div>
            <!-- divide grid into 2 -->
            <div class="flex flex-row justify-between">
                <div class="w-1/4 divide-y divide-gray-100">
                    <div class="px-4 py-6 sm:grid sm:gap-4 sm:px-0">
                        <!-- QR Code -->
                        <img src="{{ url($this->record->qr_url) }}" alt="" class="w-full" />
                    </div>
                </div>
                <div class="w-3/4 divide-y divide-gray-100" style="width: -webkit-fill-available;">
                
                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0 w-full">
                        <dt class="text-sm font-medium leading-6 text-gray-900">Nama Pembeli</dt>
                        <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                            {{ $this->record->buyer_name }}</dd>
                    </div>
                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                        <dt class="text-sm font-medium leading-6 text-gray-900">Nama Barang</dt>
                        <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                            {{ $this->record->goods->name }}</dd>
                    </div>
                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                        <dt class="text-sm font-medium leading-6 text-gray-900">Qty</dt>
                        <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">{{ $this->record->qty }}</dd>
                    </div>
                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                        <dt class="text-sm font-medium leading-6 text-gray-900">Bill Of Lading</dt>
                        <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">{{ $this->record->coo }}</dd>
                    </div>
                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                        <dt class="text-sm font-medium leading-6 text-gray-900">Certificate Of Origin</dt>
                        <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">{{ $this->record->coo }}</dd>
                    </div>
                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                        <dt class="text-sm font-medium leading-6 text-gray-900">Note</dt>
                        <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                            {{ $this->record->note }}
                        </dd>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($this->transaction)
        @livewire('table-transaction', ['sales_id' => $this->record->id])
    @endif
</x-filament-panels::page>