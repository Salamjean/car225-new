<section id="travel-hero" class="travel-hero section dark-background">

    <div class="hero-background absolute inset-0 z-0">
        <video autoplay="" muted="" loop="" class="w-full h-full object-cover">
            <source src="{{ asset('assets/img/travel/video-2.mp4') }}" type="video/mp4">
        </video>
        <div class="hero-overlay absolute inset-0 bg-black/40 pointer-events-none"></div>
    </div>

    <div class="container relative z-10 h-full flex flex-col justify-center items-center" style="min-height: 80vh;">
        <!-- Hero Text Centered -->
        <div class="text-center mb-8" data-aos="fade-up" data-aos-delay="100">
            <h1 class="text-4xl md:text-6xl font-black text-white mb-4 drop-shadow-lg tracking-tight uppercase">
                Voyagez avec vos compagnies <br/> à travers <span class="text-[#e94e1a]">Car225</span>
            </h1>
            <p class="text-xl text-gray-200 mb-8 max-w-2xl mx-auto drop-shadow-md">
                Réservez vos billets de car en toute simplicité et voyagez en sécurité partout en Côte d'Ivoire.
            </p>
        </div>

        <!-- Search Form Component -->
        <div class="w-full max-w-6xl mx-auto mb-16 px-4" data-aos="fade-up" data-aos-delay="200">
            <div class="bg-black/30 backdrop-blur-2xl rounded-[3rem] p-3 md:p-5 border border-white/10 shadow-[0_25px_80px_rgba(0,0,0,0.5)]">
                <form action="{{ route('programmes.search') }}" method="GET" class="search-form">
                    @csrf
                    
                    <!-- Trip Type Switcher (Illuminated style) -->
                    <div class="flex justify-center md:justify-start mb-6 px-4">
                        <div class="flex bg-white/5 p-1.5 rounded-2xl border border-white/5">
                            <label class="relative flex items-center px-8 py-2.5 cursor-pointer rounded-xl transition-all duration-500 has-[:checked]:bg-[#e94e1a] has-[:checked]:shadow-[0_0_20px_rgba(233,78,26,0.4)] text-gray-400 has-[:checked]:text-white font-black text-xs uppercase tracking-[0.2em]">
                                <input type="radio" name="is_aller_retour" value="0" class="peer absolute opacity-0" {{ request('is_aller_retour') != '1' ? 'checked' : '' }}>
                                <i class="fas fa-arrow-right mr-3 text-sm opacity-50"></i>
                                Simple
                            </label>
                            <label class="relative flex items-center px-8 py-2.5 cursor-pointer rounded-xl transition-all duration-500 has-[:checked]:bg-[#e94e1a] has-[:checked]:shadow-[0_0_20px_rgba(233,78,26,0.4)] text-gray-400 has-[:checked]:text-white font-black text-xs uppercase tracking-[0.2em]">
                                <input type="radio" name="is_aller_retour" value="1" class="peer absolute opacity-0" {{ request('is_aller_retour') == '1' ? 'checked' : '' }}>
                                <i class="fas fa-retweet mr-3 text-sm opacity-50"></i>
                                Retour
                            </label>
                        </div>
                    </div>

                    <div class="flex flex-col lg:flex-row lg:items-center gap-4 lg:gap-0">
                        <!-- Origin & Destination Group -->
                        <div class="flex-1 grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-0 relative">
                            <!-- Origin -->
                            <div class="group px-6 py-2 lg:border-r border-white/10 hover:bg-white/5 transition-colors duration-500 first:rounded-l-[2rem]">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 flex items-center justify-center text-[#e94e1a] text-2xl group-hover:scale-110 transition-transform">
                                        <i class="fas fa-plane-departure"></i>
                                    </div>
                                    <div class="ml-4 flex-grow">
                                        <label class="block text-[10px] uppercase tracking-widest font-black text-gray-400 mb-1">Départ</label>
                                        <input type="text" name="point_depart" id="point_depart" 
                                               class="w-full bg-transparent border-none focus:ring-0 p-0 font-bold text-white placeholder:text-gray-600 text-xl" 
                                               placeholder="Abidjan..." value="{{ old('point_depart') }}" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Swap Button (Floating) -->
                            <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 z-30 hidden lg:block">
                                <button type="button" id="swapCoordinates" class="w-12 h-12 bg-[#1a1a1a] border border-white/10 rounded-full shadow-2xl flex items-center justify-center text-white hover:text-[#e94e1a] hover:border-[#e94e1a]/50 transition-all duration-300 group">
                                    <i class="fas fa-random text-sm group-hover:rotate-180 transition-transform duration-500"></i>
                                </button>
                            </div>

                            <!-- Destination -->
                            <div class="group px-6 py-2 lg:border-r border-white/10 hover:bg-white/5 transition-colors duration-500">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 flex items-center justify-center text-[#e94e1a] text-2xl group-hover:scale-110 transition-transform">
                                        <i class="fas fa-map-marked-alt"></i>
                                    </div>
                                    <div class="ml-4 flex-grow">
                                        <label class="block text-[10px] uppercase tracking-widest font-black text-gray-400 mb-1">Arrivée</label>
                                        <input type="text" name="point_arrive" id="point_arrive" 
                                               class="w-full bg-transparent border-none focus:ring-0 p-0 font-bold text-white placeholder:text-gray-600 text-xl" 
                                               placeholder="Bouaké..." value="{{ old('point_arrive') }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Dates Group -->
                        <div class="flex flex-col sm:flex-row flex-shrink-0">
                            <!-- Outbound Date -->
                            <div class="group px-8 py-2 lg:border-r border-white/10 hover:bg-white/5 transition-colors duration-500 min-w-[200px]">
                                <label class="block text-[10px] uppercase tracking-widest font-black text-gray-400 mb-1">Date départ</label>
                                <div class="flex items-center text-white">
                                    <i class="far fa-calendar-check mr-3 text-[#e94e1a]"></i>
                                    <input type="date" name="date_depart" id="date_depart" 
                                           class="bg-transparent border-none focus:ring-0 p-0 font-bold text-lg cursor-pointer [color-scheme:dark]" 
                                           value="{{ old('date_depart', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>

                            <!-- Return Date -->
                            <div class="group px-8 py-2 lg:border-r border-white/10 hover:bg-white/5 transition-colors duration-500 min-w-[200px] hidden" id="date_retour_wrapper">
                                <label class="block text-[10px] uppercase tracking-widest font-black text-gray-400 mb-1">Date retour</label>
                                <div class="flex items-center text-white">
                                    <i class="far fa-calendar-plus mr-3 text-blue-400"></i>
                                    <input type="date" name="date_retour" id="date_retour" 
                                           class="bg-transparent border-none focus:ring-0 p-0 font-bold text-lg cursor-pointer [color-scheme:dark]" 
                                           min="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                        </div>

                        <!-- Search Action -->
                        <div class="lg:pl-6 py-2 lg:pr-2">
                            <button type="submit" class="w-full lg:w-44 h-[70px] bg-[#e94e1a] hover:bg-[#ff5a20] text-white font-black text-lg uppercase tracking-widest rounded-3xl shadow-[0_15px_35px_rgba(233,78,26,0.3)] hover:shadow-[0_20px_50px_rgba(233,78,26,0.5)] transform hover:-translate-y-1 transition-all duration-300 flex items-center justify-center gap-3">
                                <i class="fas fa-search"></i>
                                <span>Go</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- See All Programs Button -->
        <div class="text-center" data-aos="fade-up" data-aos-delay="300">
             <a href="{{ route('programmes.all') }}" class="inline-flex items-center px-8 py-3.5 border border-white/20 hover:border-white/50 bg-white/5 hover:bg-white/10 backdrop-blur-md rounded-full text-white/70 hover:text-white font-black text-xs uppercase tracking-[0.3em] transition-all duration-500 shadow-xl group">
                <i class="fas fa-th-large mr-4 text-[#e94e1a] group-hover:rotate-45 transition-transform"></i>
                Voir les voyages disponibles
            </a>
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