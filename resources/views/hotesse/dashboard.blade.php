@extends('hotesse.layouts.template')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-orange-50 py-8 px-4">
    <div class="mx-auto" style="width: 90%">
        <!-- En-tête de bienvenue -->
        <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">
                    Bienvenue, {{ $hotesse->prenom }} ! 👋
                </h1>
                <div class="flex items-center gap-2">
                    <p class="text-lg font-semibold text-[#e94e1a]">
                        {{ $stats['compagnie'] }}
                    </p>
                    @if($stats['compagnie_slogan'])
                        <span class="text-gray-400">|</span>
                        <p class="text-gray-500 italic">{{ $stats['compagnie_slogan'] }}</p>
                    @endif
                </div>
            </div>
            @if($stats['compagnie_logo'])
                <div class="flex-shrink-0">
                    <img src="{{ asset('storage/' . $stats['compagnie_logo']) }}" alt="Logo" class="h-16 w-auto object-contain rounded-lg shadow-sm bg-white p-2">
                </div>
            @endif
        </div>

        <!-- Cartes de statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- Revenu Global -->
            <div class="bg-white rounded-2xl shadow-xl p-6 border-l-4 border-green-500 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Revenu Global</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['revenu_global'], 0, ',', ' ') }} <span class="text-xs font-normal">FCFA</span></p>
                        <p class="text-xs text-gray-500 mt-2">Total des ventes</p>
                    </div>
                    <div class="w-16 h-16 bg-green-100 rounded-xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Ventes du jour -->
            <div class="bg-white rounded-2xl shadow-xl p-6 border-l-4 border-purple-500 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Ventes (Aujourd'hui)</p>
                        <p class="text-4xl font-bold text-gray-900">{{ $stats['ventes_aujourdhui'] }}</p>
                        <p class="text-xs text-gray-500 mt-2">Tickets vendus</p>
                    </div>
                    <div class="w-16 h-16 bg-purple-100 rounded-xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-1M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Revenu du jour -->
            <div class="bg-white rounded-2xl shadow-xl p-6 border-l-4 border-yellow-500 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Revenu (Aujourd'hui)</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['revenu_aujourdhui'], 0, ',', ' ') }} <span class="text-xs font-normal">FCFA</span></p>
                        <p class="text-xs text-gray-500 mt-2">Chiffre d'affaires</p>
                    </div>
                    <div class="w-16 h-16 bg-yellow-100 rounded-xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphique des ventes -->
        <div class="bg-white rounded-3xl shadow-xl p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                <svg class="w-6 h-6 mr-2 text-[#e94e1a]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                </svg>
                Évolution des Ventes (7 derniers jours)
            </h2>
            
            <div id="salesChart"></div>
        </div>

        <!-- Achat Rapide -->
        <div class="bg-gradient-to-r from-[#e94e1a] to-[#d33d0f] rounded-3xl shadow-lg p-8 text-white flex items-center justify-between hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 group cursor-pointer" onclick="window.location.href='{{ route('hotesse.vendre-ticket') }}'">
            <div>
                <h3 class="text-2xl font-bold mb-2">Vendre un Ticket</h3>
                <p class="text-white/80 text-lg">Accéder rapidement au formulaire de vente</p>
            </div>
            <div class="bg-white/20 p-4 rounded-full group-hover:bg-white/30 transition-all duration-300">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                </svg>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var options = {
            series: [{
                name: 'Ventes',
                data: @json($chartData)
            }],
            chart: {
                height: 350,
                type: 'area',
                toolbar: {
                    show: false
                },
                fontFamily: 'Inter, sans-serif'
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            xaxis: {
                categories: @json($chartLabels),
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                }
            },
            yaxis: {
                labels: {
                    formatter: function (value) {
                        return new Intl.NumberFormat('fr-FR').format(value) + " FCFA";
                    }
                }
            },
            colors: ['#e94e1a'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.2,
                    stops: [0, 90, 100]
                }
            },
            grid: {
                borderColor: '#f1f1f1',
            },
            tooltip: {
                y: {
                    formatter: function (value) {
                        return new Intl.NumberFormat('fr-FR').format(value) + " FCFA";
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#salesChart"), options);
        chart.render();
    });

    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Succès!',
            text: '{{ session('success') }}',
            confirmButtonColor: '#e94e1a',
            timer: 3000,
            timerProgressBar: true
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Erreur!',
            text: '{{ session('error') }}',
            confirmButtonColor: '#d33'
        });
    @endif
</script>
@endsection
