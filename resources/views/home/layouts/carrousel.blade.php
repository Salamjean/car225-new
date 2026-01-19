<section id="travel-hero" class="travel-hero section dark-background">

    <div class="hero-background">
        <video autoplay="" muted="" loop="">
            <source src="{{ asset('assets/img/travel/video-2.mp4') }}" type="video/mp4">
        </video>
        <div class="hero-overlay"></div>
    </div>

    <div class="container position-relative">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <div class="hero-text" data-aos="fade-up" data-aos-delay="100">
                    <h1 class="hero-title">Découvrez Votre Parfait Itinéraire</h1>
                    <p class="hero-subtitle">Trouvez les meilleurs trajets pour vos déplacements. Recherchez par point
                        de départ, point d'arrivée, date et durée de parcours pour trouver l'itinéraire idéal.</p>
                    <div class="hero-buttons">
                        <a href="#search-form" class="btn btn-primary me-3">Rechercher Maintenant</a>
                        <a href="{{ route('programmes.all') }}" class="btn btn-outline">Voir Tous les Programmes</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="booking-form-wrapper" data-aos="fade-left" data-aos-delay="200" id="search-form">
                    <div class="booking-form">
                        <h3 class="form-title">Rechercher un Programme</h3>
                        <form action="{{ route('programmes.search') }}" method="GET" class="search-programme-form">
                            @csrf

                            <div class="form-group mb-3">
                                <label for="point_depart">Point de Départ</label>
                                <input type="text" name="point_depart" id="point_depart" class="form-control"
                                    placeholder="Ex: Abidjan, Yopougon..." value="{{ old('point_depart') }}" required>
                                <div class="form-text">Entrez la ville ou le quartier de départ</div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="point_arrive">Point d'Arrivée</label>
                                <input type="text" name="point_arrive" id="point_arrive" class="form-control"
                                    placeholder="Ex: Bouaké, Daloa..." value="{{ old('point_arrive') }}" required>
                                <div class="form-text">Entrez la ville ou le quartier d'arrivée</div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="date_depart">Date de Départ</label>
                                <input type="date" name="date_depart" id="date_depart" class="form-control"
                                    value="{{ old('date_depart', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required>
                                <div class="form-text">Sélectionnez la date de voyage</div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="is_aller_retour">Type de Voyage</label>
                                <select name="is_aller_retour" id="is_aller_retour" class="form-select">
                                    <option value="">Tous les types</option>
                                    <option value="0" {{ request('is_aller_retour') == '0' ? 'selected' : '' }}>Aller
                                        Simple</option>
                                    <option value="1" {{ request('is_aller_retour') == '1' ? 'selected' : '' }}>
                                        Aller-Retour</option>
                                </select>
                                <div class="form-text">Choisissez si vous voulez un aller-retour ou non</div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-2"></i>
                                Rechercher des Programmes
                            </button>
                        </form>

                        <!-- Suggestions populaires -->
                        <div class="popular-routes mt-4">
                            <h6 class="text-center mb-3">Trajets Populaires</h6>
                            <div class="d-flex flex-wrap gap-2 justify-content-center">
                                <a href="{{ route('programmes.search', [
    'point_depart' => 'Abidjan',
    'point_arrive' => 'Bouaké',
    'date_depart' => date('Y-m-d'),
    // Retirer 'durer_parcours' du tableau
]) }}" class="badge bg-light text-dark text-decoration-none">
                                    Abidjan → Bouaké
                                </a>
                                <a href="{{ route('programmes.search', [
    'point_depart' => 'Abidjan',
    'point_arrive' => 'Yamoussoukro',
    'date_depart' => date('Y-m-d'),
]) }}" class="badge bg-light text-dark text-decoration-none">
                                    Abidjan → Yamoussoukro
                                </a>
                                <a href="{{ route('programmes.search', [
    'point_depart' => 'Abidjan',
    'point_arrive' => 'San Pedro',
    'date_depart' => date('Y-m-d'),
]) }}" class="badge bg-light text-dark text-decoration-none">
                                    Abidjan → San Pedro
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
</script>