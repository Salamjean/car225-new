@extends('user.layouts.template')

@section('title', 'Réservation Convoi')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="inline-flex bg-white border border-gray-100 rounded-2xl p-1">
            <a href="{{ route('user.convoi.create') }}" class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider bg-[#e94f1b] text-white">
                Nouveau convoi
            </a>
            <a href="{{ route('user.convoi.index') }}" class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider text-gray-600">
                Mes convois
            </a>
        </div>

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl sm:text-3xl font-black text-[#1A1D1F] tracking-tight">
                    Réservation <span class="text-[#e94f1b]">Convoi</span>
                </h1>
                <p class="text-sm text-gray-500 font-medium">Étape 1/2 : choisissez la compagnie et le nombre de personnes.</p>
            </div>
            <span class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider bg-[#e94f1b]/10 text-[#e94f1b]">
                Sans paiement
            </span>
        </div>

        @if (session('success'))
            <div class="rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-green-700 font-semibold text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-red-700 font-semibold text-sm">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-[28px] border border-gray-100 shadow-sm p-6 sm:p-8">
            <form action="{{ route('user.convoi.step-two') }}" method="POST" class="space-y-6">
                @csrf

                <div class="space-y-2">
                    <label class="block text-[11px] font-black uppercase tracking-widest text-gray-500">Compagnie</label>
                    <select id="compagnieSelect" name="compagnie_id"
                        class="w-full px-4 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-[#e94f1b] focus:bg-white outline-none text-sm font-bold">
                        <option value="">Choisir une compagnie</option>
                        @foreach ($compagnies as $compagnie)
                            <option value="{{ $compagnie->id }}" @selected(old('compagnie_id') == $compagnie->id)>
                                {{ $compagnie->name }} {{ $compagnie->sigle ? '(' . $compagnie->sigle . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('compagnie_id')
                        <p class="text-xs font-semibold text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label class="block text-[11px] font-black uppercase tracking-widest text-gray-500">Itinéraire</label>
                    <select id="itineraireSelect" name="itineraire_id"
                        class="w-full px-4 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-[#e94f1b] focus:bg-white outline-none text-sm font-bold"
                        disabled>
                        <option value="">Choisir d abord une compagnie</option>
                    </select>
                    @error('itineraire_id')
                        <p class="text-xs font-semibold text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label class="block text-[11px] font-black uppercase tracking-widest text-gray-500">Nombre de personnes</label>
                    <input type="number" name="nombre_personnes" min="1" max="100" value="{{ old('nombre_personnes', 1) }}"
                        class="w-full px-4 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-2 focus:ring-[#e94f1b] focus:bg-white outline-none text-sm font-bold">
                    @error('nombre_personnes')
                        <p class="text-xs font-semibold text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2">
                    <button type="submit"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-2xl bg-[#e94f1b] text-white text-xs font-black uppercase tracking-widest shadow-lg shadow-[#e94f1b]/20 hover:bg-[#d44518] transition-all">
                        Suivant
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            (function() {
                const compagnieSelect = document.getElementById('compagnieSelect');
                const itineraireSelect = document.getElementById('itineraireSelect');
                const oldItineraire = "{{ old('itineraire_id') }}";

                function resetItineraires(message = 'Choisir d abord une compagnie') {
                    itineraireSelect.innerHTML = `<option value="">${message}</option>`;
                    itineraireSelect.disabled = true;
                }

                function fillItineraires(items) {
                    itineraireSelect.innerHTML = '<option value="">Choisir un itinéraire</option>';
                    items.forEach((it) => {
                        const opt = document.createElement('option');
                        opt.value = it.id;
                        opt.textContent = it.label;
                        if (oldItineraire && String(oldItineraire) === String(it.id)) {
                            opt.selected = true;
                        }
                        itineraireSelect.appendChild(opt);
                    });
                    itineraireSelect.disabled = false;
                }

                async function loadItineraires(compagnieId) {
                    if (!compagnieId) {
                        resetItineraires();
                        return;
                    }

                    resetItineraires('Chargement des itinéraires...');
                    try {
                        const url = `/user/convoi/compagnie/${compagnieId}/itineraires`;
                        const res = await fetch(url, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });
                        const data = await res.json();
                        if (Array.isArray(data.itineraires) && data.itineraires.length > 0) {
                            fillItineraires(data.itineraires);
                        } else {
                            resetItineraires('Aucun itinéraire disponible');
                        }
                    } catch (e) {
                        resetItineraires('Erreur de chargement');
                    }
                }

                compagnieSelect.addEventListener('change', function() {
                    loadItineraires(this.value);
                });

                if (compagnieSelect.value) {
                    loadItineraires(compagnieSelect.value);
                }
            })();
        </script>
    @endpush
@endsection

