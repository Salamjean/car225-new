@extends('admin.layouts.template')

@section('content')
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto">

        <div class="mb-8 bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-red-100 text-red-600 rounded-2xl flex items-center justify-center text-xl shadow-inner">
                    <i class="fas fa-edit"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-black text-gray-900 tracking-tight">Modifier {{ $sapeurPompier->name }}</h1>
                    <p class="text-sm text-gray-500 font-medium mt-1">{{ $sapeurPompier->email }}</p>
                </div>
            </div>
            <a href="{{ route('sapeur-pompier.index') }}" class="flex items-center gap-2 px-5 py-2.5 bg-gray-50 hover:bg-gray-100 border border-gray-200 text-gray-700 rounded-xl font-bold">
                <i class="fas fa-arrow-left"></i> Liste
            </a>
        </div>

        @if(session('error'))
            <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded">
                <i class="fas fa-exclamation-triangle mr-1"></i>{{ session('error') }}
            </div>
        @endif

        <form action="{{ route('sapeur-pompier.update', $sapeurPompier->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- ───────────── COMBOBOX RECHERCHE (changer de localisation) ───────────── --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-red-600 to-orange-500 text-white flex items-center gap-3 rounded-t-2xl">
                    <i class="fas fa-search-location text-xl"></i>
                    <div>
                        <h2 class="font-bold">Modifier la localisation (facultatif)</h2>
                        <p class="text-xs text-red-100 mt-0.5">Recherchez une autre caserne pour mettre à jour adresse et coordonnées GPS, ou laissez tel quel.</p>
                    </div>
                </div>
                <div class="p-6">
                    <div class="relative">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 z-10"></i>
                        <input type="text" id="stationSearch" autocomplete="off"
                            placeholder="Ex: Plateau, Yopougon, Bouaké..."
                            class="block w-full pl-11 pr-12 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500/30 focus:border-red-500 focus:bg-white text-base">
                        <span id="stationLoader" class="hidden absolute right-4 top-1/2 -translate-y-1/2 text-red-600 z-10">
                            <i class="fas fa-spinner fa-spin"></i>
                        </span>

                        <div id="stationResults"
                            class="hidden absolute left-0 right-0 top-full mt-2 z-50 bg-white border border-gray-200 rounded-xl shadow-2xl max-h-96 overflow-y-auto">
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- INFOS GÉNÉRALES --}}
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-[24px] shadow-sm border border-gray-100">
                        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex items-center gap-3 rounded-t-[24px]">
                            <i class="fas fa-info-circle text-red-500"></i>
                            <h2 class="text-lg font-bold text-gray-800">Informations</h2>
                        </div>
                        <div class="p-6 space-y-5">
                            @if($sapeurPompier->path_logo)
                                <div class="flex items-center gap-3 mb-3">
                                    <img src="{{ Storage::url($sapeurPompier->path_logo) }}" class="w-16 h-16 rounded-full object-cover border border-gray-200">
                                    <p class="text-xs text-gray-500">Logo actuel — un nouveau fichier remplacera celui-ci</p>
                                </div>
                            @endif

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Nom / Caserne <span class="text-red-500">*</span></label>
                                    <input id="f_name" name="name" required value="{{ old('name', $sapeurPompier->name) }}"
                                        class="block w-full px-4 py-3 bg-gray-50 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500/20 focus:border-red-500 focus:bg-white font-medium">
                                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Email Officiel <span class="text-red-500">*</span></label>
                                    <input type="email" name="email" required value="{{ old('email', $sapeurPompier->email) }}"
                                        class="block w-full px-4 py-3 bg-gray-50 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500/20 focus:border-red-500 focus:bg-white font-medium">
                                    @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Téléphone <span class="text-red-500">*</span></label>
                                    <input name="contact" required value="{{ old('contact', $sapeurPompier->contact) }}"
                                        type="tel" inputmode="numeric" pattern="\d{10}" maxlength="10" minlength="10"
                                        title="10 chiffres exactement"
                                        oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10)"
                                        class="block w-full px-4 py-3 bg-gray-50 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500/20 focus:border-red-500 focus:bg-white font-medium">
                                    <p class="text-[11px] text-gray-400 mt-1">Format : 10 chiffres</p>
                                    @error('contact') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Logo (remplacer)</label>
                                    <input type="file" name="path_logo" accept="image/*"
                                        class="block w-full py-2.5 px-3 bg-gray-50 border border-gray-200 rounded-xl file:mr-4 file:py-1.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-600 hover:file:bg-red-100">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- GÉOLOCALISATION --}}
                <div class="space-y-6">
                    <div class="bg-white rounded-[24px] shadow-sm border border-gray-100 relative">
                        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-red-500 to-orange-500 rounded-t-[24px]"></div>

                        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex items-center gap-3 rounded-t-[24px]">
                            <i class="fas fa-map-marked-alt text-red-500"></i>
                            <h2 class="text-lg font-bold text-gray-800">Géolocalisation</h2>
                        </div>
                        <div class="p-6 space-y-5">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1.5">Commune <span class="text-red-500">*</span></label>
                                <input id="f_commune" name="commune" required value="{{ old('commune', $sapeurPompier->commune) }}"
                                    class="block w-full px-4 py-3 bg-gray-50 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500/20 focus:border-red-500 focus:bg-white font-medium">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1.5">Adresse <span class="text-red-500">*</span></label>
                                <input id="f_adresse" name="adresse" required value="{{ old('adresse', $sapeurPompier->adresse) }}"
                                    class="block w-full px-4 py-3 bg-gray-50 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500/20 focus:border-red-500 focus:bg-white font-medium">
                            </div>

                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 space-y-3">
                                <div class="flex items-center gap-2 text-xs font-bold text-gray-500 uppercase tracking-wider">
                                    <i class="fas fa-satellite text-gray-400"></i> Coordonnées GPS
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Latitude</label>
                                        <input id="f_latitude" name="latitude" value="{{ old('latitude', $sapeurPompier->latitude) }}"
                                            class="block w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Longitude</label>
                                        <input id="f_longitude" name="longitude" value="{{ old('longitude', $sapeurPompier->longitude) }}"
                                            class="block w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm font-mono">
                                    </div>
                                </div>
                                <p class="text-[10px] text-gray-400 leading-tight">
                                    <i class="fas fa-info-circle"></i> Une caserne ne peut pas être enregistrée à la même position qu'une autre caserne déjà existante.
                                </p>
                            </div>

                            <div id="mapPreview" class="hidden bg-white rounded-xl border border-gray-200 overflow-hidden">
                                <iframe id="mapFrame" width="100%" height="220" frameborder="0" style="border:0"></iframe>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full flex items-center justify-center gap-3 py-4 bg-red-600 hover:bg-red-700 text-white rounded-[20px] font-black text-lg shadow-[0_8px_20px_rgba(220,38,38,0.3)] hover:shadow-[0_12px_25px_rgba(220,38,38,0.4)] transition-all">
                        <i class="fas fa-save text-xl"></i>
                        <span>Enregistrer les modifications</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        const SEARCH_URL = '{{ route("sapeur-pompier.search-stations") }}';
        const $search   = document.getElementById('stationSearch');
        const $results  = document.getElementById('stationResults');
        const $loader   = document.getElementById('stationLoader');

        const $name     = document.getElementById('f_name');
        const $commune  = document.getElementById('f_commune');
        const $adresse  = document.getElementById('f_adresse');
        const $lat      = document.getElementById('f_latitude');
        const $lng      = document.getElementById('f_longitude');
        const $mapPrev  = document.getElementById('mapPreview');
        const $mapFrame = document.getElementById('mapFrame');

        let abortCtrl = null;
        let debounceT = null;

        async function fetchStations(q) {
            if (abortCtrl) abortCtrl.abort();
            abortCtrl = new AbortController();
            $loader.classList.remove('hidden');
            try {
                const r = await fetch(SEARCH_URL + '?q=' + encodeURIComponent(q || ''), {
                    signal: abortCtrl.signal,
                    headers: { 'Accept': 'application/json' }
                });
                const data = await r.json();
                renderResults(data.results || []);
            } catch (e) {
                if (e.name !== 'AbortError') renderResults([]);
            } finally {
                $loader.classList.add('hidden');
            }
        }

        function renderResults(items) {
            if (!items.length) {
                $results.innerHTML = '<div class="p-4 text-sm text-gray-500 text-center">Aucune caserne trouvée.</div>';
                $results.classList.remove('hidden');
                return;
            }
            $results.innerHTML = items.map((it, i) => `
                <button type="button" data-i="${i}" class="station-row w-full text-left px-4 py-3 hover:bg-red-50 border-b border-gray-100 last:border-0 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full ${it.source === 'curated' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700'} flex items-center justify-center shrink-0">
                        <i class="fas ${it.source === 'curated' ? 'fa-fire-extinguisher' : 'fa-map-marker-alt'}"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-gray-900 truncate">${escapeHtml(it.name)}</div>
                        <div class="text-xs text-gray-500 truncate">${escapeHtml(it.commune || '—')} · ${escapeHtml(it.adresse || '')}</div>
                    </div>
                    <span class="text-[10px] font-bold uppercase ${it.source === 'curated' ? 'text-red-600' : 'text-blue-600'}">${it.source === 'curated' ? 'GSPM' : 'OSM'}</span>
                </button>
            `).join('');
            $results.classList.remove('hidden');
            $results.querySelectorAll('.station-row').forEach((btn, idx) => {
                btn.addEventListener('click', () => pickStation(items[idx]));
            });
        }

        function pickStation(it) {
            $name.value    = it.name || $name.value;
            $commune.value = it.commune || '';
            $adresse.value = it.adresse || '';
            $lat.value     = (it.latitude  ?? '').toString();
            $lng.value     = (it.longitude ?? '').toString();
            $search.value  = it.name;
            $results.classList.add('hidden');
            updateMapPreview(it.latitude, it.longitude);
        }

        function updateMapPreview(lat, lng) {
            if (!lat || !lng) { $mapPrev.classList.add('hidden'); return; }
            const bbox = [
                (parseFloat(lng) - 0.005), (parseFloat(lat) - 0.005),
                (parseFloat(lng) + 0.005), (parseFloat(lat) + 0.005),
            ].join(',');
            $mapFrame.src = `https://www.openstreetmap.org/export/embed.html?bbox=${bbox}&layer=mapnik&marker=${lat},${lng}`;
            $mapPrev.classList.remove('hidden');
        }

        function escapeHtml(s) {
            return String(s ?? '').replace(/[&<>"']/g, c => ({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;' }[c]));
        }

        $search.addEventListener('input', () => {
            clearTimeout(debounceT);
            debounceT = setTimeout(() => fetchStations($search.value.trim()), 250);
        });
        $search.addEventListener('focus', () => { fetchStations($search.value.trim()); });
        document.addEventListener('click', (e) => {
            if (!$results.contains(e.target) && e.target !== $search) $results.classList.add('hidden');
        });
        [$lat, $lng].forEach(el => el.addEventListener('change', () => updateMapPreview($lat.value, $lng.value)));

        if ($lat.value && $lng.value) updateMapPreview($lat.value, $lng.value);
    </script>
@endsection
