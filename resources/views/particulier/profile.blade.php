@extends('particulier.layouts.template')

@section('page-title', 'Mon Profil')

@section('styles')
<style>
    .profile-avatar-ring {
        background: conic-gradient(#e94f1b 0deg, #f97316 120deg, #e94f1b 240deg, #f97316 360deg);
        padding: 3px;
        border-radius: 9999px;
        display: inline-block;
    }
    .profile-avatar-inner {
        border-radius: 9999px;
        overflow: hidden;
        background: white;
        padding: 3px;
    }
    .ptab-btn { transition: all .2s; }
    .ptab-btn.active {
        background: linear-gradient(135deg, #e94f1b, #f97316);
        color: white;
        box-shadow: 0 4px 14px rgba(233,79,27,.25);
    }
    .ptab-btn:not(.active) { background: #f9fafb; color: #6b7280; }
    .photo-drop-zone {
        border: 2px dashed #e5e7eb;
        border-radius: 1rem;
        transition: all .2s;
        cursor: pointer;
    }
    .photo-drop-zone:hover, .photo-drop-zone.drag-over {
        border-color: #e94f1b;
        background: #fff5f2;
    }
    .strength-bar { height: 4px; border-radius: 99px; transition: all .3s; }
</style>
@endsection

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    @if(session('success'))
    <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-4 flex items-center gap-3 shadow-sm">
        <div class="w-9 h-9 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
            <i class="fas fa-check-circle text-emerald-600"></i>
        </div>
        <p class="text-sm font-bold text-emerald-800">{{ session('success') }}</p>
    </div>
    @endif

    @if($errors->any())
    <div class="rounded-2xl bg-red-50 border border-red-200 p-4 flex items-start gap-3 shadow-sm">
        <div class="w-9 h-9 rounded-xl bg-red-100 flex items-center justify-center flex-shrink-0 mt-0.5">
            <i class="fas fa-exclamation-circle text-red-600"></i>
        </div>
        <div>
            <p class="text-sm font-black text-red-800 mb-1">Veuillez corriger les erreurs suivantes :</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li class="text-xs text-red-700 font-semibold">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    @if($particulier->must_change_password)
    <div class="rounded-2xl border-2 border-amber-300 bg-gradient-to-r from-amber-50 to-orange-50 p-5 flex items-center gap-4 shadow-md">
        <div class="w-12 h-12 flex-shrink-0 rounded-2xl bg-amber-100 flex items-center justify-center">
            <i class="fas fa-shield-alt text-amber-600 text-xl"></i>
        </div>
        <div class="flex-1">
            <p class="font-black text-amber-900 text-sm">Action requise — Changez votre mot de passe</p>
            <p class="text-xs text-amber-700 font-medium mt-0.5">Votre mot de passe actuel a ete genere automatiquement par l administration. Definissez un mot de passe personnel ci-dessous pour securiser votre compte.</p>
        </div>
        <span class="flex-shrink-0 px-3 py-1 rounded-full bg-amber-200 text-amber-800 text-[10px] font-black uppercase">
            <i class="fas fa-exclamation me-1"></i>Requis
        </span>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- LEFT --}}
        <div class="lg:col-span-1 space-y-5">
            <div class="bg-white rounded-3xl p-6 border border-gray-200/80 shadow-sm text-center space-y-4">
                <div class="relative inline-block">
                    <div class="profile-avatar-ring">
                        <div class="profile-avatar-inner w-28 h-28">
                            @if($particulier->photo_proprietaire)
                                <img src="{{ $particulier->photo_proprietaire_url }}" alt="avatar" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-[#e94f1b] to-[#f97316] text-white text-3xl font-black">
                                    {{ strtoupper(substr($particulier->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <span onclick="switchProfileTab('photo')" class="absolute bottom-1 right-1 w-7 h-7 rounded-full bg-[#e94f1b] border-2 border-white flex items-center justify-center cursor-pointer">
                        <i class="fas fa-camera text-white text-[10px]"></i>
                    </span>
                </div>
                <div>
                    <h3 class="text-base font-black text-gray-800">{{ $particulier->full_name }}</h3>
                    <p class="text-xs font-bold text-[#e94f1b] mt-0.5">{{ $particulier->code_id }}</p>
                    <div class="mt-2 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-black uppercase border border-emerald-100">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Compte Agree
                    </div>
                </div>
                <div class="text-left border-t border-gray-100 pt-4 space-y-3">
                    <div>
                        <span class="text-[10px] font-black uppercase text-gray-400 block">Email</span>
                        <span class="text-xs font-semibold text-gray-700 break-all">{{ $particulier->email }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-black uppercase text-gray-400 block">Telephone</span>
                        <span class="text-xs font-bold text-gray-700">{{ $particulier->contact }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-black uppercase text-gray-400 block">Immatriculation</span>
                        <span class="inline-flex px-2 py-0.5 mt-0.5 rounded bg-gray-100 text-gray-800 font-mono text-xs font-bold border border-gray-200">{{ $particulier->immatriculation }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-black uppercase text-gray-400 block">Capacite vehicule</span>
                        <span class="text-xs font-bold text-gray-700">{{ $particulier->nombre_place_car }} places</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-4 border {{ $particulier->must_change_password ? 'border-amber-200 bg-amber-50/50' : 'border-emerald-200 bg-emerald-50/50' }} shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl {{ $particulier->must_change_password ? 'bg-amber-100' : 'bg-emerald-100' }} flex items-center justify-center flex-shrink-0">
                        <i class="fas {{ $particulier->must_change_password ? 'fa-exclamation-triangle text-amber-600' : 'fa-lock text-emerald-600' }}"></i>
                    </div>
                    <div>
                        <p class="text-xs font-black {{ $particulier->must_change_password ? 'text-amber-800' : 'text-emerald-800' }}">
                            {{ $particulier->must_change_password ? 'Mot de passe a changer' : 'Mot de passe personnalise' }}
                        </p>
                        <p class="text-[10px] font-medium {{ $particulier->must_change_password ? 'text-amber-600' : 'text-emerald-600' }}">
                            {{ $particulier->must_change_password ? 'Definissez un mot de passe personnel' : 'Votre compte est securise' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT --}}
        <div class="lg:col-span-2 space-y-5">

            <div class="bg-white rounded-2xl p-1.5 border border-gray-200/80 shadow-sm flex gap-1">
                <button onclick="switchProfileTab('password')" id="tab-password-btn" class="ptab-btn active flex-1 px-4 py-2.5 rounded-xl text-xs font-black uppercase tracking-wider">
                    <i class="fas fa-key me-1.5"></i> Mot de passe
                </button>
                <button onclick="switchProfileTab('photo')" id="tab-photo-btn" class="ptab-btn flex-1 px-4 py-2.5 rounded-xl text-xs font-black uppercase tracking-wider">
                    <i class="fas fa-camera me-1.5"></i> Photo de profil
                </button>
                <button onclick="switchProfileTab('info')" id="tab-info-btn" class="ptab-btn flex-1 px-4 py-2.5 rounded-xl text-xs font-black uppercase tracking-wider">
                    <i class="fas fa-bus me-1.5"></i> Vehicule
                </button>
            </div>

            {{-- Password Tab --}}
            <div id="tab-password" class="bg-white rounded-3xl p-6 border border-gray-200/80 shadow-sm space-y-5">
                <div class="flex items-center gap-3 border-b border-gray-100 pb-4">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#e94f1b] to-[#f97316] flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-key text-white"></i>
                    </div>
                    <div>
                        <h4 class="font-black text-gray-800 text-sm">Changer le mot de passe</h4>
                        <p class="text-xs text-gray-400 font-medium">Minimum 8 caracteres recommandes</p>
                    </div>
                </div>
                <form action="{{ route('particulier.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-wider text-gray-500 mb-1.5">
                            <i class="fas fa-envelope me-1 text-[#e94f1b]"></i> Adresse email <span class="text-gray-400 font-normal normal-case">(optionnel)</span>
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="{{ $particulier->email }}"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#e94f1b]/30 focus:border-[#e94f1b] transition-all">
                        <p class="text-[10px] text-gray-400 mt-1">Laissez vide pour conserver votre email actuel.</p>
                    </div>
                    <div class="h-px bg-gray-100"></div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-wider text-gray-500 mb-1.5">
                            <i class="fas fa-lock me-1 text-[#e94f1b]"></i> Mot de passe actuel
                        </label>
                        <div class="relative">
                            <input type="password" name="current_password" id="current_password" placeholder="llllllllll"
                                class="w-full px-4 py-3 pr-12 bg-gray-50 border {{ $errors->has('current_password') ? 'border-red-400' : 'border-gray-200' }} rounded-xl text-sm font-semibold text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#e94f1b]/30 focus:border-[#e94f1b] transition-all">
                            <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#e94f1b] transition-colors" onclick="togglePwd('current_password', this)">
                                <i class="fas fa-eye text-sm"></i>
                            </button>
                        </div>
                        @error('current_password')
                            <p class="text-xs text-red-600 font-semibold mt-1"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</p>
                        @enderror
                        <p class="text-[10px] text-gray-400 mt-1">Requis uniquement si vous souhaitez changer le mot de passe.</p>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-wider text-gray-500 mb-1.5">
                            <i class="fas fa-lock-open me-1 text-emerald-500"></i> Nouveau mot de passe
                        </label>
                        <div class="relative">
                            <input type="password" name="new_password" id="new_password" placeholder="Minimum 8 caracteres"
                                oninput="checkStrength(this.value)"
                                class="w-full px-4 py-3 pr-12 bg-gray-50 border {{ $errors->has('new_password') ? 'border-red-400' : 'border-gray-200' }} rounded-xl text-sm font-semibold text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#e94f1b]/30 focus:border-[#e94f1b] transition-all">
                            <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#e94f1b] transition-colors" onclick="togglePwd('new_password', this)">
                                <i class="fas fa-eye text-sm"></i>
                            </button>
                        </div>
                        <div class="mt-2 space-y-1" id="strengthWrapper" style="display:none">
                            <div class="flex gap-1">
                                <div class="strength-bar flex-1 bg-gray-200" id="s1"></div>
                                <div class="strength-bar flex-1 bg-gray-200" id="s2"></div>
                                <div class="strength-bar flex-1 bg-gray-200" id="s3"></div>
                                <div class="strength-bar flex-1 bg-gray-200" id="s4"></div>
                            </div>
                            <p class="text-[10px] font-bold" id="strengthLabel"></p>
                        </div>
                        @error('new_password')
                            <p class="text-xs text-red-600 font-semibold mt-1"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-wider text-gray-500 mb-1.5">
                            <i class="fas fa-check-double me-1 text-emerald-500"></i> Confirmer le nouveau mot de passe
                        </label>
                        <div class="relative">
                            <input type="password" name="new_password_confirmation" id="new_password_confirmation"
                                placeholder="Repetez le nouveau mot de passe" oninput="checkMatch()"
                                class="w-full px-4 py-3 pr-12 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#e94f1b]/30 focus:border-[#e94f1b] transition-all">
                            <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#e94f1b] transition-colors" onclick="togglePwd('new_password_confirmation', this)">
                                <i class="fas fa-eye text-sm"></i>
                            </button>
                        </div>
                        <p class="text-[10px] font-semibold mt-1 hidden" id="matchMsg"></p>
                    </div>
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-2xl font-black text-xs uppercase tracking-wider text-white transition-all shadow-lg shadow-[#e94f1b]/20" style="background:linear-gradient(135deg,#e94f1b,#f97316);">
                        <i class="fas fa-save"></i> Enregistrer les modifications
                    </button>
                </form>
            </div>

            {{-- Photo Tab --}}
            <div id="tab-photo" class="bg-white rounded-3xl p-6 border border-gray-200/80 shadow-sm space-y-5 hidden">
                <div class="flex items-center gap-3 border-b border-gray-100 pb-4">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-camera text-white"></i>
                    </div>
                    <div>
                        <h4 class="font-black text-gray-800 text-sm">Mettre a jour la photo de profil</h4>
                        <p class="text-xs text-gray-400 font-medium">JPG, PNG ou WebP — max 2 Mo</p>
                    </div>
                </div>
                <form action="{{ route('particulier.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    <div class="flex flex-col items-center gap-4">
                        <div class="w-32 h-32 rounded-2xl overflow-hidden border-4 border-gray-100 shadow-md bg-gray-50" id="photoPreviewWrap">
                            @if($particulier->photo_proprietaire)
                                <img src="{{ $particulier->photo_proprietaire_url }}" alt="Photo actuelle" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-[#e94f1b] to-[#f97316] text-white text-4xl font-black">
                                    {{ strtoupper(substr($particulier->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <p class="text-xs text-gray-400 font-semibold">Apercu en temps reel</p>
                    </div>
                    <label for="photoInput" class="photo-drop-zone block p-8 text-center" id="dropZone">
                        <input type="file" name="photo_proprietaire" id="photoInput" accept="image/*" class="hidden" onchange="previewPhoto(this)">
                        <div class="space-y-3">
                            <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center mx-auto">
                                <i class="fas fa-cloud-upload-alt text-blue-500 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm font-black text-gray-700">Glissez votre photo ici</p>
                                <p class="text-xs text-gray-400 font-medium mt-0.5">ou <span class="text-[#e94f1b] font-bold">parcourir les fichiers</span></p>
                            </div>
                            <p class="text-[10px] text-gray-400">JPG, PNG, WebP. Max 2 Mo</p>
                        </div>
                    </label>
                    <p class="text-[10px] text-gray-400 text-center" id="fileName"></p>
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-2xl font-black text-xs uppercase tracking-wider text-white transition-all shadow-lg" style="background:linear-gradient(135deg,#3b82f6,#6366f1);">
                        <i class="fas fa-upload"></i> Mettre a jour la photo
                    </button>
                </form>
            </div>

            {{-- Info Tab --}}
            <div id="tab-info" class="space-y-5 hidden">
                <div class="bg-white rounded-3xl p-6 border border-gray-200/80 shadow-sm space-y-5">
                    <div class="flex items-center gap-3 border-b border-gray-100 pb-4">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#e94f1b] to-[#f97316] flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-bus text-white"></i>
                        </div>
                        <div>
                            <h4 class="font-black text-gray-800 text-sm">Caracteristiques du Vehicule</h4>
                            <p class="text-xs text-gray-400 font-medium">Informations enregistrees lors de l inscription</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="p-4 rounded-2xl bg-orange-50/60 border border-orange-100 text-center">
                            <i class="fas fa-users text-[#e94f1b] text-xl mb-2"></i>
                            <p class="text-[10px] font-black uppercase text-gray-400">Capacite</p>
                            <p class="text-2xl font-black text-gray-800">{{ $particulier->nombre_place_car }}</p>
                            <p class="text-[10px] text-gray-400">places</p>
                        </div>
                        <div class="p-4 rounded-2xl bg-blue-50/60 border border-blue-100 text-center">
                            <i class="fas fa-id-card text-blue-500 text-xl mb-2"></i>
                            <p class="text-[10px] font-black uppercase text-gray-400">Immatriculation</p>
                            <p class="text-sm font-mono font-black text-gray-800 mt-1">{{ $particulier->immatriculation }}</p>
                        </div>
                        <div class="p-4 rounded-2xl bg-emerald-50/60 border border-emerald-100 text-center">
                            <i class="fas fa-calendar-check text-emerald-500 text-xl mb-2"></i>
                            <p class="text-[10px] font-black uppercase text-gray-400">Mise en service</p>
                            <p class="text-sm font-black text-gray-800 mt-1">{{ $particulier->date_mise_service->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-3xl p-6 border border-gray-200/80 shadow-sm space-y-4">
                    <h4 class="font-black text-gray-800 text-sm border-b border-gray-100 pb-3">
                        <i class="fas fa-images text-[#e94f1b] me-2"></i> Photos du Vehicule
                    </h4>
                    <div class="grid grid-cols-3 gap-3">
                        @foreach([['photo_complete_car','Vue Complete','photo_complete_car_url'],['photo_avant_car','Vue Avant','photo_avant_car_url'],['photo_arriere_car','Vue Arriere','photo_arriere_car_url']] as [$field,$label,$urlM])
                        <div class="rounded-2xl overflow-hidden border border-gray-200/60 shadow-sm">
                            <div class="bg-gray-50 text-[9px] font-black text-gray-500 p-2 text-center border-b uppercase">{{ $label }}</div>
                            <div class="h-28 bg-gray-100">
                                @if($particulier->$field)
                                    <a href="{{ $particulier->$urlM }}" target="_blank">
                                        <img src="{{ $particulier->$urlM }}" alt="{{ $label }}" class="w-full h-full object-cover hover:scale-105 transition-all duration-300">
                                    </a>
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-300"><i class="fas fa-image text-2xl"></i></div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="bg-white rounded-3xl p-6 border border-gray-200/80 shadow-sm space-y-4">
                    <h4 class="font-black text-gray-800 text-sm border-b border-gray-100 pb-3">
                        <i class="fas fa-file-contract text-[#e94f1b] me-2"></i> Pieces Administratives
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="p-4 bg-gray-50 border border-gray-100 rounded-2xl flex flex-col gap-3">
                            <div>
                                <h5 class="font-bold text-gray-800 text-sm mb-1"><i class="fas fa-car text-[#e94f1b] me-1"></i> Carte Grise</h5>
                                <p class="text-xs text-gray-400 font-semibold">Document d immatriculation officiel</p>
                            </div>
                            @if($particulier->carte_grise)
                            <a href="{{ $particulier->carte_grise_url }}" target="_blank" class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl bg-white border border-gray-200 hover:bg-gray-100 text-gray-700 text-xs font-bold transition-all shadow-sm">
                                <i class="fas fa-external-link-alt"></i> Ouvrir la piece
                            </a>
                            @endif
                        </div>
                        <div class="p-4 bg-gray-50 border border-gray-100 rounded-2xl flex flex-col gap-3">
                            <div>
                                <h5 class="font-bold text-gray-800 text-sm mb-1"><i class="fas fa-clipboard-check text-[#e94f1b] me-1"></i> Visite Technique</h5>
                                <p class="text-xs text-gray-400 font-semibold">Attestation de conformite routiere</p>
                            </div>
                            @if($particulier->visite_technique)
                            <a href="{{ $particulier->visite_technique_url }}" target="_blank" class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl bg-white border border-gray-200 hover:bg-gray-100 text-gray-700 text-xs font-bold transition-all shadow-sm">
                                <i class="fas fa-external-link-alt"></i> Ouvrir la piece
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function switchProfileTab(tab) {
        ['password','photo','info'].forEach(t => {
            document.getElementById('tab-'+t)?.classList.add('hidden');
            document.getElementById('tab-'+t+'-btn')?.classList.remove('active');
        });
        document.getElementById('tab-'+tab)?.classList.remove('hidden');
        document.getElementById('tab-'+tab+'-btn')?.classList.add('active');
    }
    @if($particulier->must_change_password) switchProfileTab('password'); @endif

    function togglePwd(id, btn) {
        const inp = document.getElementById(id);
        const icon = btn.querySelector('i');
        if (inp.type === 'password') { inp.type = 'text'; icon.classList.replace('fa-eye','fa-eye-slash'); }
        else { inp.type = 'password'; icon.classList.replace('fa-eye-slash','fa-eye'); }
    }

    function checkStrength(val) {
        const w = document.getElementById('strengthWrapper');
        if (!val) { w.style.display='none'; return; }
        w.style.display='block';
        let s=0;
        if(val.length>=8) s++;
        if(val.length>=12) s++;
        if(/[A-Z]/.test(val)||/[0-9]/.test(val)) s++;
        if(/[^A-Za-z0-9]/.test(val)) s++;
        const c=['#ef4444','#f97316','#eab308','#22c55e'];
        const l=['Tres faible','Faible','Moyen','Fort'];
        const lc=['text-red-500','text-orange-500','text-yellow-600','text-emerald-600'];
        ['s1','s2','s3','s4'].forEach((id,i)=>{ document.getElementById(id).style.background=i<s?c[s-1]:'#e5e7eb'; });
        const lbl=document.getElementById('strengthLabel');
        lbl.textContent=l[s-1]??''; lbl.className='text-[10px] font-bold '+(lc[s-1]??'');
        checkMatch();
    }

    function checkMatch() {
        const pwd=document.getElementById('new_password').value;
        const conf=document.getElementById('new_password_confirmation').value;
        const msg=document.getElementById('matchMsg');
        if(!conf){msg.classList.add('hidden');return;}
        if(pwd===conf){msg.textContent='Les mots de passe correspondent';msg.className='text-[10px] font-semibold text-emerald-600';}
        else{msg.textContent='Les mots de passe ne correspondent pas';msg.className='text-[10px] font-semibold text-red-500';}
        msg.classList.remove('hidden');
    }

    function previewPhoto(input) {
        if(input.files&&input.files[0]){
            const r=new FileReader();
            r.onload=e=>{document.getElementById('photoPreviewWrap').innerHTML=`<img src="${e.target.result}" class="w-full h-full object-cover" alt="Apercu">`;};
            r.readAsDataURL(input.files[0]);
            document.getElementById('fileName').textContent='Fichier selectionne: '+input.files[0].name;
        }
    }
    const dz=document.getElementById('dropZone'),pi=document.getElementById('photoInput');
    if(dz){
        dz.addEventListener('dragover',e=>{e.preventDefault();dz.classList.add('drag-over');});
        dz.addEventListener('dragleave',()=>dz.classList.remove('drag-over'));
        dz.addEventListener('drop',e=>{e.preventDefault();dz.classList.remove('drag-over');if(e.dataTransfer.files[0]){pi.files=e.dataTransfer.files;previewPhoto(pi);}});
    }
</script>
@endsection