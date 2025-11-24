@php
    $fieldId = $fieldId ?? uniqid('media_field_');
    $buttonText = $buttonText ?? 'Wybierz z biblioteki';
    $buttonClass = $buttonClass ?? 'w-full mt-3 border border-dashed border-blue-400 rounded-lg py-3 text-blue-600 font-semibold hover:bg-blue-50 transition-colors';
    $previewImageId = $previewImageId ?? null;
    $previewWrapperId = $previewWrapperId ?? null;
    $fileNameId = $fileNameId ?? null;
@endphp

<div
    x-data="mediaPicker({
        listUrl: '{{ route('admin.media.list') }}',
        fieldId: '{{ $fieldId }}',
        previewImageId: '{{ $previewImageId }}',
        previewWrapperId: '{{ $previewWrapperId }}',
        fileNameId: '{{ $fileNameId }}'
    })">
    <button type="button" class="{{ $buttonClass }}" @click="open">
        <i class="fa-solid fa-photo-film mr-2"></i> {{ $buttonText }}
    </button>

    <div x-show="isOpen"
         x-transition.opacity
         class="fixed inset-0 z-40 bg-black/50 flex items-center justify-center p-4"
         style="display: none;">
        <div class="bg-white rounded-2xl shadow-2xl max-w-5xl w-full h-[80vh] flex flex-col relative">
            <button class="absolute top-4 right-4 text-gray-400 hover:text-gray-600" @click="close">
                <i class="fa-solid fa-xmark text-2xl"></i>
            </button>

            <div class="p-6 border-b border-gray-100">
                <h3 class="text-xl font-semibold text-gray-900">Biblioteka mediów</h3>
                <p class="text-sm text-gray-500 mt-1">Kliknij w plik aby podstawić go w formularzu.</p>
                <div class="mt-4 flex items-center gap-3">
                    <input type="text" x-model="search"
                           class="flex-1 border border-gray-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Szukaj po nazwie lub tagu...">
                    <button type="button" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm"
                            @click="reload">
                        <i class="fa-solid fa-rotate mr-1"></i> Odśwież
                    </button>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-6">
                <template x-if="loading">
                    <div class="h-full flex items-center justify-center text-gray-500">
                        <i class="fa-solid fa-spinner fa-spin mr-2"></i> Ładowanie plików...
                    </div>
                </template>

                <template x-if="!loading && filteredItems.length === 0">
                    <div class="h-full flex flex-col items-center justify-center text-gray-500">
                        <i class="fa-regular fa-images text-4xl mb-3"></i>
                        <p>Brak plików spełniających kryteria.</p>
                    </div>
                </template>

                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4"
                     x-show="!loading && filteredItems.length > 0"
                     x-cloak>
                    <template x-for="item in filteredItems" :key="item.id">
                        <div class="border border-gray-200 rounded-2xl overflow-hidden flex flex-col">
                            <div class="h-40 bg-gray-50 flex items-center justify-center relative">
                                <img x-show="item.is_image"
                                     :src="item.url"
                                     :alt="item.alt_text || item.filename"
                                     class="w-full h-full object-cover">
                                <i x-show="!item.is_image" class="fa-solid fa-file text-4xl text-gray-400"></i>
                                <span class="absolute top-3 right-3 bg-white/90 text-[10px] font-semibold px-2 py-1 rounded-full uppercase">
                                    <span x-text="item.extension || 'plik'"></span>
                                </span>
                            </div>
                            <div class="p-4 flex-1 flex flex-col text-sm text-gray-600 space-y-2">
                                <p class="font-semibold text-gray-900 truncate" :title="item.filename" x-text="item.filename"></p>
                                <p class="text-xs text-gray-500" x-text="item.size + (item.width ? ' • ' + item.width + 'x' + item.height + 'px' : '')"></p>
                                <p class="text-xs" x-text="item.alt_text ? 'ALT: ' + item.alt_text : 'ALT: brak'"></p>
                                <div class="flex flex-wrap gap-1">
                                    <template x-if="item.tags.length === 0">
                                        <span class="text-xs text-gray-400">brak tagów</span>
                                    </template>
                                    <template x-for="tag in item.tags" :key="tag">
                                        <span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-700 text-[11px]">#<span x-text="tag"></span></span>
                                    </template>
                                </div>
                                <div class="mt-auto flex flex-col gap-2">
                                    <button type="button"
                                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-lg"
                                            @click="select(item)">
                                        Użyj w formularzu
                                    </button>
                                    <button type="button"
                                            class="w-full bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold py-2 rounded-lg"
                                            @click="copy(item.url)">
                                        Kopiuj URL
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>

@once
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('mediaPicker', (config) => ({
                isOpen: false,
                loading: false,
                items: [],
                search: '',
                open() {
                    this.isOpen = true;
                    if (this.items.length === 0) {
                        this.loadItems();
                    }
                },
                close() {
                    this.isOpen = false;
                },
                async reload() {
                    this.items = [];
                    await this.loadItems();
                },
                async loadItems() {
                    this.loading = true;
                    try {
                        const response = await fetch(config.listUrl);
                        this.items = await response.json();
                    } catch (e) {
                        alert('Nie udało się pobrać mediów.');
                    } finally {
                        this.loading = false;
                    }
                },
                get filteredItems() {
                    if (!this.search) {
                        return this.items;
                    }
                    const query = this.search.toLowerCase();
                    return this.items.filter(item =>
                        item.filename.toLowerCase().includes(query) ||
                        (item.tags || []).some(tag => tag.toLowerCase().includes(query))
                    );
                },
                select(item) {
                    const field = document.getElementById(config.fieldId);
                    if (field) {
                        field.value = item.path;
                        field.dispatchEvent(new Event('input'));
                    }

                    if (config.previewImageId) {
                        const img = document.getElementById(config.previewImageId);
                        if (img) {
                            img.src = item.url;
                        }
                    }

                    if (config.previewWrapperId) {
                        const wrapper = document.getElementById(config.previewWrapperId);
                        if (wrapper) {
                            wrapper.classList.remove('hidden');
                        }
                    }

                    if (config.fileNameId) {
                        const label = document.getElementById(config.fileNameId);
                        if (label) {
                            label.textContent = item.filename;
                            label.classList.remove('hidden');
                        }
                    }

                    this.close();
                },
                copy(url) {
                    if (!navigator.clipboard) {
                        const tmp = document.createElement('input');
                        tmp.value = url;
                        document.body.appendChild(tmp);
                        tmp.select();
                        document.execCommand('copy');
                        document.body.removeChild(tmp);
                    } else {
                        navigator.clipboard.writeText(url);
                    }
                    alert('Skopiowano adres do schowka.');
                }
            }));
        });
    </script>
@endonce

