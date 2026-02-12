<!DOCTYPE html>
<html>

<head>
    <title>URGENCE - Accident Signalé</title>
</head>

<body>
    <h1>Signalement d'Urgence</h1>
    <p>Un accident a été signalé à proximité de votre position.</p>

    <h2>Détails:</h2>
    <ul>
        <li><strong>Type:</strong> {{ $signalement->type }}</li>
        <li><strong>Description:</strong> {{ $signalement->description }}</li>
        <li><strong>Date/Heure:</strong> {{ $signalement->created_at }}</li>
        <li><strong>Lieu (Coords):</strong> {{ $signalement->latitude }}, {{ $signalement->longitude }}</li>
        <li><strong>Google Maps:</strong> <a
                href="https://www.google.com/maps/search/?api=1&query={{ $signalement->latitude }},{{ $signalement->longitude }}">Voir
                sur la carte</a></li>
    </ul>

    <h2>Informations du Voyage:</h2>
    <ul>
        <li><strong>Départ:</strong> {{ $programme->point_depart }}</li>
        <li><strong>Arrivée:</strong> {{ $programme->point_arrive }}</li>
        <li><strong>Compagnie:</strong> {{ $programme->compagnie->name ?? 'N/A' }}</li>
        <li><strong>Véhicule:</strong> {{ $programme->vehicule->immatriculation ?? 'N/A' }}</li>
    </ul>

    <h2>Contact Signaleur:</h2>
    <ul>
        <li><strong>Nom:</strong> {{ $user->name }}</li>
        <li><strong>Contact:</strong> {{ $user->contact ?? $user->email }}</li>
    </ul>
</body>

</html>