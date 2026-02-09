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
        <div class="w-full max-w-6xl mx-auto mb-8" data-aos="fade-up" data-aos-delay="200">
            <div class="bg-white rounded-lg shadow-2xl p-2 md:p-4">
                <form action="{{ route('programmes.search') }}" method="GET" class="search-form">
                    @csrf
                    
                    <!-- Trip Type Selection -->
                    <div class="flex items-center gap-6 mb-4 px-2">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <div class="relative flex items-center">
                                <input type="radio" name="is_aller_retour" value="0" class="peer h-5 w-5 border-2 border-gray-300 text-[#e94e1a] focus:ring-[#e94e1a]" {{ request('is_aller_retour') != '1' ? 'checked' : '' }}>
                            </div>
                            <span class="text-gray-700 font-medium group-hover:text-gray-900">Aller simple</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <div class="relative flex items-center">
                                <input type="radio" name="is_aller_retour" value="1" class="peer h-5 w-5 border-2 border-gray-300 text-[#e94e1a] focus:ring-[#e94e1a]" {{ request('is_aller_retour') == '1' ? 'checked' : '' }}>
                            </div>
                            <span class="text-gray-700 font-medium group-hover:text-gray-900">Aller-retour</span>
                        </label>
                    </div>

                    <!-- Main Search Inputs -->
                    <div class="flex flex-col md:flex-row items-stretch gap-1">
                        <!-- DE -->
                        <div class="relative flex-1 group">
                            <label class="absolute text-xs text-gray-500 top-2 left-10 z-10">De</label>
                            <div class="flex items-center h-14 bg-gray-50 border border-gray-200 rounded-lg group-hover:bg-gray-100 transition-colors focus-within:ring-2 focus-within:ring-[#e94e1a] focus-within:border-transparent">
                                <span class="pl-3 text-gray-400">
                                    <i class="fas fa-map-marker-alt text-lg"></i>
                                </span>
                                <input type="text" name="point_depart" id="point_depart" class="w-full h-full bg-transparent border-none focus:ring-0 px-3 pt-4 font-semibold text-gray-900 placeholder-transparent" placeholder="Ville de départ" value="{{ old('point_depart') }}" required>
                            </div>
                        </div>

                        <!-- Swap Button -->
                        <div class="flex items-center justify-center -my-2 md:my-0 md:-mx-3 z-20">
                            <button type="button" id="swapCoordinates" class="w-10 h-10 bg-white border border-gray-200 rounded-full shadow-md flex items-center justify-center text-gray-400 hover:text-[#e94e1a] hover:bg-gray-50 transition-all transform hover:rotate-180">
                                <i class="fas fa-exchange-alt"></i>
                            </button>
                        </div>

                        <!-- A -->
                        <div class="relative flex-1 group">
                            <label class="absolute text-xs text-gray-500 top-2 left-10 z-10">À</label>
                            <div class="flex items-center h-14 bg-gray-50 border border-gray-200 rounded-lg group-hover:bg-gray-100 transition-colors focus-within:ring-2 focus-within:ring-[#e94e1a] focus-within:border-transparent">
                                <span class="pl-3 text-gray-400">
                                    <i class="fas fa-map-marker-alt text-lg"></i>
                                </span>
                                <input type="text" name="point_arrive" id="point_arrive" class="w-full h-full bg-transparent border-none focus:ring-0 px-3 pt-4 font-semibold text-gray-900 placeholder-transparent" placeholder="Ville d'arrivée" value="{{ old('point_arrive') }}" required>
                            </div>
                        </div>

                        <!-- Date Départ -->
                        <div class="relative w-full md:w-48 group">
                            <label class="absolute text-xs text-gray-500 top-2 left-10 z-10">Départ</label>
                            <div class="flex items-center h-14 bg-gray-50 border border-gray-200 rounded-lg group-hover:bg-gray-100 transition-colors focus-within:ring-2 focus-within:ring-[#e94e1a] focus-within:border-transparent">
                                <span class="pl-3 text-gray-400">
                                    <i class="far fa-calendar-alt text-lg"></i>
                                </span>
                                <input type="date" name="date_depart" id="date_depart" class="w-full h-full bg-transparent border-none focus:ring-0 px-3 pt-4 font-semibold text-gray-900" value="{{ old('date_depart', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>

                        <!-- Date Retour (Hidden by default) -->
                        <div class="relative w-full md:w-48 group hidden" id="date_retour_wrapper">
                            <label class="absolute text-xs text-gray-500 top-2 left-10 z-10">Retour</label>
                            <div class="flex items-center h-14 bg-gray-50 border border-gray-200 rounded-lg group-hover:bg-gray-100 transition-colors focus-within:ring-2 focus-within:ring-[#e94e1a] focus-within:border-transparent">
                                <span class="pl-3 text-gray-400">
                                    <i class="far fa-calendar-alt text-lg"></i>
                                </span>
                                <input type="date" name="date_retour" id="date_retour" class="w-full h-full bg-transparent border-none focus:ring-0 px-3 pt-4 font-semibold text-gray-900" min="{{ date('Y-m-d') }}">
                            </div>
                        </div>

                        <!-- Search Button -->
                        <div class="w-full md:w-40">
                            <button type="submit" class="w-full h-14 bg-[#e94e1a] hover:bg-[#d33d0f] text-white font-bold text-lg rounded-lg shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5 flex items-center justify-center">
                                Chercher
                            </button>
                        </div>
                    </div>

                    <!-- Footer Options -->
                    <div class="mt-4 flex items-center gap-2 px-2 text-sm text-gray-600">
            
                    </div>
                </form>
            </div>
        </div>

        <!-- See All Programs Button -->
        <div class="text-center" data-aos="fade-up" data-aos-delay="300">
             <a href="{{ route('programmes.all') }}" class="inline-flex items-center justify-center px-8 py-3 border-2 border-white text-base font-bold rounded-full text-white hover:bg-white hover:text-[#e94e1a] transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                <i class="fas fa-bus-alt mr-2"></i>
                Voir Tous les Programmes
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