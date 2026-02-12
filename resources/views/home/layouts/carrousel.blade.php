<section id="travel-hero" class="travel-hero section relative overflow-visible" style="min-height: 600px; padding-top: 0; background: url('{{ asset('assets/images/Bus avec Numéro Plaque.png') }}') center/cover no-repeat;">
    
    <!-- Dark Overlay -->
    <div class="absolute inset-0 bg-black/10 pointer-events-none"></div>

    <div class="container relative z-20 h-full flex flex-col justify-end" style="min-height: 600px;">
        
        <div class="w-full max-w-6xl mx-auto px-4 translate-y-24">
            <!-- Centered Title right above search bar -->
            <div class="text-left mb-4" data-aos="fade-up">
                <h2 class="text-3xl md:text-5xl font-black text-white drop-shadow-[0_2px_8px_rgba(0,0,0,0.6)] tracking-tight">
                    Réservez vos billets de car en toute simplicité et voyagez en sécurité partout en Côte d'Ivoire
                </h2>
            </div>

            <!-- Search Form Component - Overlapping the bottom edge -->
            <div class="bg-white rounded-[2rem] p-4 md:p-6 shadow-[0_30px_60px_rgba(0,0,0,0.25)] border border-gray-100" data-aos="fade-up" data-aos-delay="200">
                <form action="{{ route('programmes.search') }}" method="GET" class="search-form">
                    @csrf
                    
                    <!-- Trip Type Switcher & All Voyages Link -->
                    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                        <div class="flex bg-gray-100 p-1 rounded-xl">
                            <label class="relative flex items-center px-6 py-2 cursor-pointer rounded-lg transition-all has-[:checked]:bg-[#e94e1a] text-gray-500 has-[:checked]:text-white font-bold text-xs uppercase tracking-wider">
                                <input type="radio" name="is_aller_retour" value="0" class="peer absolute opacity-0" {{ request('is_aller_retour') != '1' ? 'checked' : '' }}>
                                Simple
                            </label>
                            <label class="relative flex items-center px-6 py-2 cursor-pointer rounded-lg transition-all has-[:checked]:bg-[#e94e1a] text-gray-500 has-[:checked]:text-white font-bold text-xs uppercase tracking-wider">
                                <input type="radio" name="is_aller_retour" value="1" class="peer absolute opacity-0" {{ request('is_aller_retour') == '1' ? 'checked' : '' }}>
                                Retour
                            </label>
                        </div>

                        <!-- Integrated "See All" Link -->
                        <a href="{{ route('programmes.all') }}" class="text-[#e94e1a] font-black text-xs uppercase tracking-[0.1em] hover:underline flex items-center gap-2">
                            <i class="fas fa-th-large"></i>
                            Voir les voyages disponibles
                        </a>
                    </div>

                    <div class="flex flex-col lg:flex-row lg:items-center gap-2">
                        <!-- Origin & Destination Group -->
                        <div class="flex-1 grid grid-cols-1 lg:grid-cols-2 gap-2 relative">
                            <div class="bg-gray-50 rounded-xl px-4 py-3 border border-gray-100 hover:border-[#e94e1a]/30 transition-all">
                                <label class="block text-[10px] uppercase font-bold text-gray-400 mb-1">De</label>
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-location-dot text-[#e94e1a]"></i>
                                    <input type="text" name="point_depart" id="point_depart" 
                                           class="w-full bg-transparent border-none focus:ring-0 p-0 font-bold text-gray-800 placeholder:text-gray-300 text-lg" 
                                           placeholder="Ville de départ..." value="{{ old('point_depart') }}" required>
                                </div>
                            </div>

                            <!-- Swap Button -->
                            <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 z-30 hidden lg:block">
                                <button type="button" id="swapCoordinates" class="w-10 h-10 bg-white border border-gray-200 rounded-full shadow-md flex items-center justify-center text-gray-400 hover:text-[#e94e1a] hover:border-[#e94e1a]/50 transition-all duration-300 group">
                                    <i class="fas fa-random text-sm group-hover:rotate-180 transition-transform duration-500"></i>
                                </button>
                            </div>

                            <div class="bg-gray-50 rounded-xl px-4 py-3 border border-gray-100 hover:border-[#e94e1a]/30 transition-all">
                                <label class="block text-[10px] uppercase font-bold text-gray-400 mb-1">À</label>
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-map-marker-alt text-[#e94e1a]"></i>
                                    <input type="text" name="point_arrive" id="point_arrive" 
                                           class="w-full bg-transparent border-none focus:ring-0 p-0 font-bold text-gray-800 placeholder:text-gray-300 text-lg" 
                                           placeholder="Destination..." value="{{ old('point_arrive') }}" required>
                                </div>
                            </div>
                        </div>

                        <!-- Dates Group -->
                        <div class="flex flex-col sm:flex-row gap-2">
                            <div class="bg-gray-50 rounded-xl px-4 py-3 border border-gray-100 hover:border-[#e94e1a]/30 transition-all min-w-[180px]">
                                <label class="block text-[10px] uppercase font-bold text-gray-400 mb-1">Départ</label>
                                <div class="flex items-center gap-3 text-gray-800">
                                    <i class="far fa-calendar text-[#e94e1a]"></i>
                                    <input type="date" name="date_depart" id="date_depart" 
                                           class="bg-transparent border-none focus:ring-0 p-0 font-bold cursor-pointer" 
                                           value="{{ old('date_depart', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>

                            <div class="bg-gray-50 rounded-xl px-4 py-3 border border-gray-100 hover:border-[#e94e1a]/30 transition-all min-w-[180px] hidden" id="date_retour_wrapper">
                                <label class="block text-[10px] uppercase font-bold text-gray-400 mb-1">Retour</label>
                                <div class="flex items-center gap-3 text-gray-800">
                                    <i class="far fa-calendar-plus text-blue-500"></i>
                                    <input type="date" name="date_retour" id="date_retour" 
                                           class="bg-transparent border-none focus:ring-0 p-0 font-bold cursor-pointer" 
                                           min="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                        </div>

                        <!-- Search Button -->
                        <button type="submit" class="bg-[#e94e1a] hover:bg-[#d14316] text-white font-black text-lg uppercase tracking-widest rounded-xl px-10 py-4 h-[70px] transition-all shadow-lg hover:shadow-[#e94e1a]/30 lg:ml-2 flex items-center justify-center gap-3">
                            <i class="fas fa-search"></i>
                            <span>Go</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Intégration Google Maps Autocomplete -->
<script
    src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&libraries=places&loading=async&callback=initAutocomplete"
    async defer></script>

<script>
    function initAutocomplete() {
        const options = {
            componentRestrictions: { country: "ci" }, // Restreindre à la Côte d'Ivoire
            fields: ["formatted_address", "geometry", "name"],
        };

        const inputDepart = document.getElementById("point_depart");
        const inputArrive = document.getElementById("point_arrive");

        if (inputDepart) {
            new google.maps.places.Autocomplete(inputDepart, options);
        }

        if (inputArrive) {
            new google.maps.places.Autocomplete(inputArrive, options);
        }
    }

    document.getElementById('swapCoordinates')?.addEventListener('click', function() {
        const depart = document.getElementById('point_depart');
        const arrive = document.getElementById('point_arrive');
        
        // Animation effect
        this.style.transform = 'rotate(180deg)';
        setTimeout(() => {
            this.style.transform = 'rotate(0deg)';
        }, 300);

        // Swap values
        const temp = depart.value;
        depart.value = arrive.value;
        arrive.value = temp;
    });

    // Handle Round Trip Visibility
    const tripTypeRadios = document.querySelectorAll('input[name="is_aller_retour"]');
    const returnDateWrapper = document.getElementById('date_retour_wrapper');
    const returnDateInput = document.getElementById('date_retour');

    function toggleReturnDate() {
        const isRoundTrip = document.querySelector('input[name="is_aller_retour"]:checked').value === '1';
        if (isRoundTrip) {
            returnDateWrapper.classList.remove('hidden');
            returnDateInput.setAttribute('required', 'required');
        } else {
            returnDateWrapper.classList.add('hidden');
            returnDateInput.removeAttribute('required');
            returnDateInput.value = ''; // Clear value when hiding
        }
    }

    tripTypeRadios.forEach(radio => {
        radio.addEventListener('change', toggleReturnDate);
    });

    // Run on load to set initial state
    toggleReturnDate();

</script>