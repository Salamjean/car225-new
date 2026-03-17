<section id="travel-hero" class="travel-hero section relative overflow-visible z-[50]" style="min-height: 600px; padding-top: 0;">
    
    <!-- Background Slider - Restricted overflow to its own container -->
    <div class="absolute inset-0 z-0 overflow-hidden">
        <div class="swiper hero-slider h-full w-full">
            <div class="swiper-wrapper">
                <!-- Slide 1 -->
                <div class="swiper-slide">
                    <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ asset('assets/images/Bus avec Numéro Plaque.png') }}');"></div>
                </div>
                <!-- Slide 2 -->
                <div class="swiper-slide">
                    <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ asset('assets/images/_Image 3 copy.png') }}');"></div>
                </div>
                <!-- Slide 3 -->
                <div class="swiper-slide">
                    <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ asset('assets/images/Bus avec Plaque Immatriculation (1).png') }}');"></div>
                </div>
            </div>
            <!-- Optional: Overlay gradient to make text readable on all slides -->
            <div class="absolute inset-0 bg-black/20 z-10 pointer-events-none"></div>
        </div>
    </div>

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
                                Aller
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
                                           placeholder="Ville de départ..." value="{{ old('point_depart') }}" required autocomplete="off">
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
                                           placeholder="Destination..." value="{{ old('point_arrive') }}" required autocomplete="off">
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

<!-- Custom Autocomplete to replace Google Maps -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        setupLocalAutocomplete("point_depart");
        setupLocalAutocomplete("point_arrive");
    });

    function setupLocalAutocomplete(inputId) {
        const input = document.getElementById(inputId);
        if (!input) return;

        // Container suggestions setup
        const container = document.createElement('div');
        container.className = 'absolute left-0 right-0 z-[100] bg-white border border-gray-100 rounded-xl shadow-2xl mt-2 max-h-60 overflow-y-auto hidden';
        
        // Find the parent div with relative positioning
        // In carrousel.blade.php, it's .bg-gray-50
        const parentDiv = input.closest('.bg-gray-50') || input.parentElement;
        if (parentDiv) {
            if (!getComputedStyle(parentDiv).position || getComputedStyle(parentDiv).position === 'static') {
                parentDiv.style.position = 'relative';
            }
            parentDiv.appendChild(container);
        }

        let locations = [];
        let currentIndex = -1;

        // Fetch all locations once
        function fetchLocations(query = '') {
            fetch(`/api/locations?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    renderSuggestions(data);
                });
        }

        function renderSuggestions(data) {
            container.innerHTML = '';
            currentIndex = -1;
            
            if (data.length > 0) {
                data.forEach((location, index) => {
                    const div = document.createElement('div');
                    div.className = 'suggestion-item px-4 py-3 hover:bg-orange-50 cursor-pointer text-gray-700 font-bold transition-colors border-b border-gray-50 last:border-0 flex items-center justify-between group';
                    div.dataset.index = index;
                    div.innerHTML = `
                        <div class="flex items-center gap-3">
                            <i class="fas fa-map-marker-alt text-[#e94e1a] text-xs opacity-50 group-hover:opacity-100"></i>
                            <span>${location}</span>
                        </div>
                        <i class="fas fa-chevron-right text-[10px] text-gray-300 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                    `;
                    div.addEventListener('click', () => {
                        input.value = location;
                        container.classList.add('hidden');
                        input.dispatchEvent(new Event('change'));
                    });
                    container.appendChild(div);
                });
                container.classList.remove('hidden');
            } else {
                container.classList.add('hidden');
            }
        }

        input.addEventListener('input', function() {
            fetchLocations(this.value);
        });

        input.addEventListener('focus', function() {
            fetchLocations(this.value);
        });

        // Keyboard navigation
        input.addEventListener('keydown', function(e) {
            const items = container.querySelectorAll('.suggestion-item');
            if (container.classList.contains('hidden') || !items.length) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                currentIndex = (currentIndex + 1) % items.length;
                updateHighlight(items);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                currentIndex = (currentIndex - 1 + items.length) % items.length;
                updateHighlight(items);
            } else if (e.key === 'Enter' && currentIndex >= 0) {
                e.preventDefault();
                items[currentIndex].click();
            } else if (e.key === 'Escape') {
                container.classList.add('hidden');
            }
        });

        function updateHighlight(items) {
            items.forEach((item, index) => {
                if (index === currentIndex) {
                    item.classList.add('bg-orange-50');
                    item.scrollIntoView({ block: 'nearest' });
                } else {
                    item.classList.remove('bg-orange-50');
                }
            });
        }

        // Close on outside click
        document.addEventListener('click', function(e) {
            if (!parentDiv.contains(e.target)) {
                container.classList.add('hidden');
            }
        });
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
        const checkedRadio = document.querySelector('input[name="is_aller_retour"]:checked');
        if (!checkedRadio) return;
        
        const isRoundTrip = checkedRadio.value === '1';
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

    // Initialize Hero Slider
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Swiper !== 'undefined') {
            new Swiper('.hero-slider', {
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
                loop: true,
                speed: 1000,
                grabCursor: true,
            });
        }
    });

</script>