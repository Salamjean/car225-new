@extends('home.layouts.template')

@section('content')

@php
    $incontournables = $compagnies->take(2);
    $groupedCompagnies = $compagnies->sortBy('name')->groupBy(function($item) {
        return strtoupper(substr($item->name, 0, 1));
    });
    $letters = $groupedCompagnies->keys();

    // Fonction Helper for tag generation
    $getCompanyTag = function($id) {
        $tags = [
            0 => ['label' => 'PREMIUM', 'class' => 'bg-orange-100 text-[#f15a24] border border-orange-200'],
            1 => ['label' => 'LUXE', 'class' => 'bg-purple-100 text-purple-500 border border-purple-200'],
            2 => ['label' => 'ECONOMIQUE', 'class' => 'bg-orange-100 text-[#f15a24] border border-orange-200'], 
            3 => ['label' => 'STANDARD', 'class' => 'bg-slate-100 text-slate-500 border border-slate-200'],
        ];
        return $tags[$id % 4];
    };
@endphp

<!-- Hero / Header -->
<section class="bg-slate-50 pt-28 pb-8">
    <div class="container mx-auto px-4 max-w-[1000px]">
        <div class="text-center mb-10">
            <h1 class="text-[2.5rem] md:text-5xl font-black text-slate-900 mb-4 tracking-tight">
                Nos <span class="text-[#0e743a]">Compagnies</span>
            </h1>
            <p class="text-slate-700 text-[15px] font-medium">
                Découvrez les meilleures compagnies de transport partenaires
            </p>
        </div>

        <!-- Search Bar -->
        <div class="max-w-[700px] mx-auto mb-6">
            <div class="relative shadow-sm rounded-xl overflow-hidden bg-white border border-slate-200">
                <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                    <i class="bi bi-search text-slate-400"></i>
                </div>
                <input type="text" id="companySearch" class="bg-white text-slate-900 text-[15px] rounded-xl block w-full pl-11 px-4 py-3.5 focus:outline-none focus:ring-1 focus:ring-emerald-500" placeholder="Tapez le nom d'une compagnie ( ex: GTI, Sotra...)">
            </div>
        </div>

     
    </div>
</section>

<!-- Main List Section -->
<section class="bg-slate-50 pb-20 relative">
    <div class="container mx-auto px-4 max-w-[1000px] relative flex gap-8">
        
        <!-- Left Content (Main) -->
        <div class="flex-1 pb-10">
            
            <!-- Les Incontournables -->
            @if($incontournables->isNotEmpty())
            <div class="mt-8 mb-12">
                <h3 class="text-[17px] font-extrabold flex items-center gap-2 text-slate-800 mb-6">
                    <i class="bi bi-award text-[#f15a24] text-xl"></i> Les incontournables
                </h3>
                <div class="space-y-4">
                    @foreach($incontournables as $compagnie)
                    <div class="bg-white border hover:border-orange-300 shadow-sm transition-all rounded-xl p-5 border-[#fadfc2]">
                        <div class="flex flex-col sm:flex-row items-center gap-6">
                            <!-- Logo -->
                            <div class="w-16 h-16 flex-shrink-0 bg-[#f8f9fa] border border-slate-100 rounded-lg flex items-center justify-center p-2">
                                @if($compagnie->path_logo)
                                    <img src="{{ asset('storage/' . $compagnie->path_logo) }}" alt="Logo {{ $compagnie->name }}" class="max-w-full max-h-full object-contain">
                                @else
                                    <i class="bi bi-bus-front-fill text-3xl text-slate-700"></i>
                                @endif
                            </div>
                            
                            <!-- Info -->
                            <div class="flex-1 text-center sm:text-left">
                                <div class="flex flex-col sm:flex-row sm:items-center gap-2 mb-1">
                                    <h4 class="text-lg font-extrabold text-[#111]">{{ $compagnie->name }}</h4>
                                    <span class="text-[8px] font-black tracking-widest px-2 py-0.5 rounded-sm bg-[#fff2e8] text-[#f15a24] w-max mx-auto sm:mx-0 uppercase">PREMIUM</span>
                                </div>
                                <div class="text-[13px] font-bold mt-1 text-slate-800">
                                    <i class="bi bi-star-fill text-[#ffc107] text-[15px]"></i> 4.8 <span class="text-slate-500 font-medium text-[11px]">(1250 avis)</span>
                                </div>
                            </div>

                            <!-- Action -->
                            <div class="mt-4 sm:mt-0">
                                <a href="{{ route('user.dashboard') }}" class="text-[#f15a24] hover:text-[#d84e1b] font-bold text-sm flex items-center gap-1 group">
                                    Voir les trajets <i class="bi bi-chevron-right text-xs group-hover:translate-x-1 transition-transform"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Alphabetical List -->
            @foreach($groupedCompagnies as $letter => $group)
            <div id="section-{{ $letter }}" class="alphabet-section pt-0">
                
                <!-- Divider -->
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-10 h-10 bg-white border border-[#eeeff1] rounded-lg flex items-center justify-center text-[#f15a24] font-black text-xl shadow-sm z-10">
                        {{ $letter }}
                    </div>
                    <div class="flex-1 h-px bg-[#e5e7eb] -ml-2"></div>
                    <div class="text-[10px] font-black text-slate-700 uppercase tracking-widest">{{ $group->count() }} COMPAGNIE{{ $group->count() > 1 ? 'S' : '' }}</div>
                </div>

                <!-- Companies -->
                <div class="space-y-4 mb-12">
                    @foreach($group as $compagnie)
                    @php $tag = $getCompanyTag($compagnie->id); @endphp
                    <div class="company-item bg-white border border-[#eeeff1] hover:border-[#f15a24] shadow-sm transition-all rounded-xl p-6" data-name="{{ strtolower($compagnie->name) }}" data-tag="{{ $tag['label'] }}">
                        <div class="flex flex-col sm:flex-row items-center sm:items-stretch gap-6">
                            
                            <!-- Logo -->
                            <div class="w-20 h-20 flex-shrink-0 bg-[#f8f9fa] border border-slate-100 rounded-lg flex items-center justify-center p-3 self-center">
                                @if($compagnie->path_logo)
                                    <img src="{{ asset('storage/' . $compagnie->path_logo) }}" alt="Logo {{ $compagnie->name }}" class="max-w-full max-h-full object-contain">
                                @else
                                    <i class="bi bi-bus-front-fill text-4xl text-slate-700"></i>
                                @endif
                            </div>

                            <!-- Info -->
                            <div class="flex-1 text-center sm:text-left flex flex-col justify-center">
                                <div class="flex flex-col sm:flex-row justify-between sm:items-start mb-2">
                                    <div>
                                        <div class="flex flex-col sm:flex-row sm:items-center gap-2 mb-1.5">
                                            <h4 class="text-xl font-extrabold text-[#111]">{{ $compagnie->name }}</h4>
                                            <span class="text-[8px] font-black tracking-widest px-2 py-0.5 rounded-sm {{ $tag['class'] }} w-max mx-auto sm:mx-0">{{ $tag['label'] }}</span>
                                        </div>
                                        <p class="text-slate-500 text-[13px] italic font-medium">"{{ $compagnie->slogan ?? 'L\'excellence du voyage avec un confort inégalé.' }}"</p>
                                    </div>
                                    <div class="text-[13px] font-bold text-slate-800 sm:text-right mt-3 sm:mt-0 flex flex-row items-center justify-center sm:flex-col sm:items-end gap-1">
                                        <div class="flex items-center gap-1">
                                            <i class="bi bi-star-fill text-[#ffc107] text-[15px]"></i> 4.8 
                                            <span class="text-slate-500 font-medium text-[11px]">(1100 avis)</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-4 flex flex-col sm:flex-row items-center sm:items-end justify-between gap-4">
                                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-4 text-[13px] font-medium text-slate-600">
                                        <div class="flex items-center gap-1">
                                            <i class="bi bi-geo text-[#f15a24]"></i> {{ $compagnie->commune ?? 'Sud, Nord' }}
                                        </div>
                                        <div class="flex items-center gap-1 font-semibold text-slate-600">
                                            <i class="bi bi-truck-front text-[#f15a24]"></i> {{ rand(15, 60) }} trajets disponibles
                                        </div>
                                    </div>
                                    <a href="{{ route('user.dashboard') }}" class="px-5 py-2.5 bg-[#eb511b] text-white hover:bg-[#d84e1b] font-bold rounded-lg text-sm shadow-sm transition-colors w-full sm:w-auto text-center flex items-center justify-center gap-1.5">
                                        Réserver <i class="bi bi-chevron-right text-[11px] mt-0.5"></i>
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach

        </div>

        <!-- Right Alpha Nav (Sticky) -->
        <div class="hidden lg:block w-10 sticky top-[120px] self-start mt-8">
            <div class="bg-white border border-[#eeeff1] rounded-2xl py-2 flex flex-col items-center gap-2 shadow-sm">
                @foreach($letters as $index => $letter)
                <a href="#section-{{ $letter }}" class="w-7 h-7 rounded-sm flex items-center justify-center text-[10px] font-black transition-colors {{ $index === 0 ? 'bg-[#f15a24] text-white' : 'text-slate-700 hover:bg-slate-100' }}" onclick="updateActiveLetter(this, '{{ $letter }}')">
                    {{ $letter }}
                </a>
                @endforeach
            </div>
        </div>

    </div>
</section>

@endsection

@push('styles')
<style>
    body {
        padding-top: 0 !important;
        scroll-behavior: smooth;
    }
    ::placeholder {
        color: #9ca3af !important;
        font-weight: 500;
    }
</style>
<script>
    function updateActiveLetter(el, letter) {
        document.querySelectorAll('.hidden.lg\\:block .rounded-sm').forEach(a => {
            a.className = "w-7 h-7 rounded-sm flex items-center justify-center text-[10px] font-black transition-colors text-slate-700 hover:bg-slate-100";
        });
        el.className = "w-7 h-7 rounded-sm flex items-center justify-center text-[10px] font-black transition-colors bg-[#f15a24] text-white";
    }

    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('companySearch');
        const items = document.querySelectorAll('.company-item');
        const sections = document.querySelectorAll('.alphabet-section');
        const filterBtns = document.querySelectorAll('.filter-btn');

        let currentSearch = '';
        let currentFilter = 'all';

        function filterCompanies() {
            items.forEach(item => {
                const nameMatch = item.dataset.name.includes(currentSearch);
                const tagMatch = currentFilter === 'all' || item.dataset.tag === currentFilter;
                
                if(nameMatch && tagMatch) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });

            // Hide sections with no visible items
            sections.forEach(section => {
                const hasVisibleItemsNoStyle = section.querySelectorAll('.company-item:not([style*="display: none"])').length;
                if(hasVisibleItemsNoStyle > 0) {
                    section.style.display = 'block';
                } else {
                    section.style.display = 'none';
                }
            });
        }

        if(searchInput) {
            searchInput.addEventListener('input', function(e) {
                currentSearch = e.target.value.toLowerCase();
                filterCompanies();
            });
        }

        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                // Update active state
                filterBtns.forEach(b => {
                    b.classList.remove('bg-black', 'text-white', 'hover:bg-slate-800');
                    b.classList.add('bg-[#dadbdf]', 'text-slate-800', 'hover:bg-slate-300');
                });
                this.classList.remove('bg-[#dadbdf]', 'text-slate-800', 'hover:bg-slate-300');
                this.classList.add('bg-black', 'text-white', 'hover:bg-slate-800');

                currentFilter = this.dataset.filter;
                filterCompanies();
            });
        });
    });
</script>
@endpush