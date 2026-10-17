<x-app-layout>
    <x-slot:title>Pesan Tiket - {{ $ticketType->name }}</x-slot:title>

    <div class="py-12 md:py-16 bg-hf-pattern min-h-screen">
        <div class="max-w-3xl mx-auto px-4 sm:px-6">
            
            <!-- Breadcrumbs / Back button -->
            <div class="mb-6">
                <a href="{{ route('tickets.index') }}" class="inline-flex items-center gap-2 font-display text-xs sm:text-sm text-hf-yellow-400 hover:text-white bg-hf-purple-900/90 px-4 py-2 rounded-xl border-2 border-hf-purple-900 shadow-hard-sm">
                    <span>← Ganti Kategori Tiket</span>
                </a>
            </div>

            <!-- Main Order Form Card -->
            <div class="bg-white text-hf-purple-900 border-4 border-hf-purple-900 rounded-3xl shadow-hard-lg overflow-hidden">
                
                <!-- Card Header -->
                <div class="p-6 bg-hf-purple-900 text-white border-b-4 border-hf-purple-900 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <span class="inline-block px-3 py-0.5 mb-1.5 text-xs font-bold tracking-wider text-hf-purple-900 uppercase bg-hf-yellow-400 border border-hf-purple-900 rounded-full">
                            FORM PEMESANAN TIKET
                        </span>
                        <h1 class="font-display text-2xl sm:text-3xl text-hf-yellow-400 uppercase tracking-wide">
                            {{ $ticketType->name }}
                        </h1>
                        <p class="text-xs text-purple-200 mt-1">
                            {{ $ticketType->description }}
                        </p>
                    </div>

                    <div class="text-right sm:border-l-2 sm:border-purple-700 sm:pl-6 shrink-0">
                        <span class="text-xs text-purple-300 block">Harga Satuan:</span>
                        <span class="font-display text-2xl sm:text-3xl text-white">
                            Rp{{ number_format($ticketType->price, 0, ',', '.') }}
                        </span>
                        <span class="text-[11px] text-purple-300 block">
                            (1 paket = {{ $ticketType->people_per_ticket }} orang)
                        </span>
                    </div>
                </div>

                <!-- Form Body with Alpine.js reactivity -->
                <form action="{{ route('orders.store') }}" 
                      method="POST" 
                      enctype="multipart/form-data"
                      x-data="{
                          qty: {{ old('quantity', 1) }},
                          price: {{ $ticketType->price }},
                          maxQty: {{ min(config('harfest.max_qty_per_order', 10), $ticketType->remaining_quota) }},
                          imagePreview: null,
                          updatePreview(event) {
                              const file = event.target.files[0];
                              if (file) {
                                  this.imagePreview = URL.createObjectURL(file);
                              } else {
                                  this.imagePreview = null;
                              }
                          }
                      }"
                      class="p-6 sm:p-8 space-y-6">
                    @csrf

                    <input type="hidden" name="ticket_type_id" value="{{ $ticketType->id }}">

                    <!-- Pemesan Info (Readonly) -->
                    <div class="p-4 bg-purple-50 rounded-2xl border-2 border-purple-200 text-xs sm:text-sm font-sans flex flex-col sm:flex-row justify-between gap-2">
                        <div>
                            <span class="text-gray-500 block">Nama Pemesan:</span>
                            <strong class="text-hf-purple-900 font-bold text-sm sm:text-base">{{ Auth::user()->name }}</strong>
                        </div>
                        <div>
                            <span class="text-gray-500 block">Kontak / HP:</span>
                            <strong class="text-hf-purple-900 font-bold">{{ Auth::user()->phone }}</strong>
                        </div>
                        <div>
                            <span class="text-gray-500 block">Email:</span>
                            <strong class="text-hf-purple-900 font-bold">{{ Auth::user()->email }}</strong>
                        </div>
                    </div>

                    <!-- Quantity Input -->
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <x-input-label for="quantity" value="Jumlah Paket Tiket" />
                            <span class="text-xs font-bold text-hf-pink-500">
                                Sisa Kuota: {{ $ticketType->remaining_quota }} paket
                            </span>
                        </div>

                        <div class="flex items-center gap-3">
                            <button type="button" 
                                    @click="if (qty > 1) qty--"
                                    class="w-12 h-12 bg-hf-purple-100 text-hf-purple-900 font-display text-2xl rounded-2xl border-3 border-hf-purple-900 shadow-hard-sm flex items-center justify-center btn-press hover:bg-hf-yellow-400">
                                -
                            </button>

                            <input id="quantity" 
                                   type="number" 
                                   name="quantity" 
                                   x-model="qty"
                                   min="1" 
                                   :max="maxQty"
                                   required 
                                   class="w-24 text-center text-xl font-display font-bold py-2 border-3 border-hf-purple-900 rounded-2xl shadow-hard-sm focus:ring-4 focus:ring-hf-yellow-400" />

                            <button type="button" 
                                    @click="if (qty < maxQty) qty++"
                                    class="w-12 h-12 bg-hf-purple-100 text-hf-purple-900 font-display text-2xl rounded-2xl border-3 border-hf-purple-900 shadow-hard-sm flex items-center justify-center btn-press hover:bg-hf-yellow-400">
                                +
                            </button>

                            <span class="text-xs text-gray-500 font-sans ml-2">
                                (Maksimal <span x-text="maxQty"></span> paket per pesanan)
                            </span>
                        </div>
                        <x-input-error :messages="$errors->get('quantity')" class="mt-2 text-sm font-bold text-rose-600" />

                        <!-- Ticket Count Indicator -->
                        <p class="text-xs text-purple-700 font-sans font-medium mt-2">
                            🎟️ Pesanan ini akan menghasilkan: <strong x-text="qty * {{ $ticketType->people_per_ticket }}"></strong> E-Tiket QR individual (1 tiket per orang).
                        </p>
                    </div>

                    <!-- IF REQUIRES STUDENT ID (CIVITAS) -->
                    @if ($ticketType->requires_student_id)
                        <div class="p-5 bg-pink-50 rounded-2xl border-3 border-dashed border-hf-pink-500 space-y-4">
                            <div class="flex items-center gap-2 text-hf-pink-500 font-display text-lg">
                                <span>🎓</span>
                                <span>Verifikasi Civitas Akademika</span>
                            </div>
                            <p class="text-xs text-gray-600 font-sans">
                                Kategori ini khusus civitas Universitas Harkat Negeri. Masukkan NIM/ID dan unggah foto kartu identitas/KTM aktif.
                            </p>

                            <!-- NIM / ID -->
                            <div>
                                <x-input-label for="student_id" value="NIM / NIP / ID Civitas *" />
                                <x-text-input id="student_id" 
                                              type="text" 
                                              name="student_id" 
                                              :value="old('student_id')" 
                                              required 
                                              class="w-full bg-white" 
                                              placeholder="Contoh: 2311500123" />
                                <x-input-error :messages="$errors->get('student_id')" class="mt-1 text-sm font-bold text-rose-600" />
                            </div>

                            <!-- Foto KTM -->
                            <div>
                                <x-input-label for="student_card" value="Unggah Foto KTM / Kartu Identitas * (JPG, PNG maks 2MB)" />
                                <input id="student_card" 
                                       type="file" 
                                       name="student_card" 
                                       accept="image/jpeg,image/png,image/jpg"
                                       required
                                       @change="updatePreview"
                                       class="block w-full text-sm text-gray-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-2 file:border-hf-purple-900 file:text-xs file:font-display file:uppercase file:bg-hf-yellow-400 file:text-hf-purple-900 file:shadow-hard-sm hover:file:bg-hf-yellow-500 cursor-pointer" />
                                <x-input-error :messages="$errors->get('student_card')" class="mt-1 text-sm font-bold text-rose-600" />

                                <!-- Preview Image -->
                                <template x-if="imagePreview">
                                    <div class="mt-3">
                                        <p class="text-xs font-bold text-gray-500 mb-1">Pratinjau Foto KTM:</p>
                                        <div class="w-48 h-32 rounded-xl overflow-hidden border-2 border-hf-purple-900 shadow-hard-sm bg-gray-100">
                                            <img :src="imagePreview" alt="Pratinjau KTM" class="w-full h-full object-cover">
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    @endif

                    <!-- Total Price Calculation Card -->
                    <div class="p-5 bg-hf-yellow-400 text-hf-purple-900 rounded-2xl border-3 border-hf-purple-900 shadow-hard flex flex-col sm:flex-row justify-between sm:items-center gap-3">
                        <div>
                            <span class="text-xs font-display uppercase tracking-wider block font-bold">Total Pembayaran</span>
                            <span class="text-xs text-purple-900/80 font-sans">
                                (<span x-text="qty"></span> paket × Rp{{ number_format($ticketType->price, 0, ',', '.') }})
                            </span>
                        </div>

                        <div class="text-left sm:text-right">
                            <span class="font-display text-3xl sm:text-4xl font-black">
                                Rp<span x-text="(qty * price).toLocaleString('id-ID')"></span>
                            </span>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button type="submit" 
                                class="w-full py-4 bg-hf-yellow-400 text-hf-purple-900 border-3 border-hf-purple-900 rounded-2xl font-display text-lg sm:text-xl uppercase tracking-wider shadow-hard-lg btn-press hover:bg-hf-yellow-500 active:bg-hf-yellow-400 flex items-center justify-center gap-2">
                            <span>Lanjut ke Pembayaran</span>
                            <span>🔥</span>
                        </button>

                        <p class="text-center text-xs text-gray-500 font-sans mt-3">
                            Setelah tombol ditekan, kuota akan dikunci dan batas waktu pembayaran adalah 24 jam.
                        </p>
                    </div>

                </form>
            </div>

        </div>
    </div>
</x-app-layout>
