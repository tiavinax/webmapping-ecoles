<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin — Ajouter une école</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: #f0f2f5; }

        /* NAVBAR */
        nav {
            background: #2e7d32;
            color: white;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        nav h1 { font-size: 18px; }
        nav a {
            color: white;
            text-decoration: none;
            background: #c62828;
            padding: 6px 14px;
            border-radius: 5px;
            font-size: 14px;
        }

        /* LAYOUT */
        .container {
            display: flex;
            height: calc(100vh - 50px);
        }

        /* CARTE */
        #map {
            flex: 1;
            height: 100%;
        }

        /* PANNEAU FORMULAIRE */
        .panel {
            width: 350px;
            background: white;
            padding: 20px;
            overflow-y: auto;
            box-shadow: -2px 0 8px rgba(0,0,0,0.1);
        }
        .panel h2 {
            color: #2e7d32;
            margin-bottom: 15px;
            font-size: 16px;
        }
        .info-clic {
            background: #e8f5e9;
            border-left: 4px solid #2e7d32;
            padding: 10px;
            margin-bottom: 15px;
            font-size: 13px;
            color: #333;
            border-radius: 3px;
        }
        label {
            display: block;
            font-size: 13px;
            color: #555;
            margin-top: 12px;
            margin-bottom: 4px;
        }
        input, select {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }
        input:focus, select:focus {
            border-color: #2e7d32;
            outline: none;
        }
        .coord-box {
            display: flex;
            gap: 8px;
        }
        .coord-box input {
            background: #f9f9f9;
            color: #888;
        }
        .btn-submit {
            width: 100%;
            margin-top: 20px;
            padding: 11px;
            background: #2e7d32;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            cursor: pointer;
        }
        .btn-submit:hover { background: #1b5e20; }
        .message {
            margin-top: 12px;
            padding: 10px;
            border-radius: 5px;
            font-size: 13px;
            text-align: center;
        }
        .success { background: #e8f5e9; color: #2e7d32; }
        .error   { background: #ffebee; color: #c62828; }
    </style>
</head>
<body>

<nav>
    <h1> Admin — Écoles Andoharanofotsy</h1>
    <a href="logout.php">Se déconnecter</a>
</nav>

<div class="container">

    <!-- CARTE LEAFLET -->
    <div id="map"></div>

    <!-- FORMULAIRE -->
    <div class="panel">
        <h2> Ajouter une école</h2>
        <div class="info-clic">
             Clique sur la carte pour placer l'école et récupérer ses coordonnées automatiquement.
        </div>

        <form id="formAjout">
            <label>Nom de l'école *</label>
            <input type="text" name="nom" id="nom" placeholder="Ex: EPP Andoharanofotsy" required>

            <label>Type *</label>
            <select name="type" id="type" required>
                <option value="">-- Choisir --</option>
                <option value="primaire">Primaire</option>
                <option value="collège">Collège</option>
                <option value="lycée">Lycée</option>
            </select>

            <label>Statut *</label>
            <select name="statut" id="statut" required>
                <option value="">-- Choisir --</option>
                <option value="public">Public</option>
                <option value="privé">Privé</option>
            </select>

            <label>Fokontany</label>
            <input type="text" name="fokontany" id="fokontany" placeholder="Ex: Andoharanofotsy I">

            <label>Téléphone</label>
            <input type="text" name="telephone" id="telephone" placeholder="Ex: 034 00 000 00">

            <label>Nombre d'élèves</label>
            <input type="number" name="nb_eleves" id="nb_eleves" placeholder="Ex: 320">

            <label>Coordonnées (clic sur carte)</label>
            <div class="coord-box">
                <input type="text" name="latitude"  id="latitude"  placeholder="Latitude"  readonly>
                <input type="text" name="longitude" id="longitude" placeholder="Longitude" readonly>
            </div>

            <button type="submit" class="btn-submit"> Enregistrer l'école</button>
        </form>

        <div id="message"></div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // Initialiser la carte centrée sur Andoharanofotsy
    const map = L.map('map').setView([-18.9500, 47.5400], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    let marker = null;

    // Clic sur la carte → récupérer lat/lon
    map.on('click', function(e) {
        const lat = e.latlng.lat.toFixed(6);
        const lng = e.latlng.lng.toFixed(6);

        document.getElementById('latitude').value  = lat;
        document.getElementById('longitude').value = lng;

        // Déplacer ou créer le marker
        if (marker) {
            marker.setLatLng(e.latlng);
        } else {
            marker = L.marker(e.latlng).addTo(map);
        }
        marker.bindPopup(" École ici").openPopup();
    });

    // Soumission du formulaire
    document.getElementById('formAjout').addEventListener('submit', function(e) {
        e.preventDefault();

        const lat = document.getElementById('latitude').value;
        const lng = document.getElementById('longitude').value;

        if (!lat || !lng) {
            document.getElementById('message').innerHTML =
                "<div class='message error'> Clique sur la carte pour choisir la position !</div>";
            return;
        }

        const data = {
            nom:       document.getElementById('nom').value,
            type:      document.getElementById('type').value,
            statut:    document.getElementById('statut').value,
            fokontany: document.getElementById('fokontany').value,
            telephone: document.getElementById('telephone').value,
            nb_eleves: document.getElementById('nb_eleves').value,
            latitude:  lat,
            longitude: lng
        };

        fetch('save_ecole.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(res => res.text().then(text => {
            // Essayer de parser JSON, sinon renvoyer le texte brut comme erreur
            try {
                return text ? JSON.parse(text) : { success: false, error: 'Réponse vide du serveur' };
            } catch (e) {
                return { success: false, error: text || 'Réponse non valide du serveur' };
            }
        }))
        .then(result => {
            if (result.success) {
                document.getElementById('message').innerHTML =
                    "<div class='message success'> École ajoutée avec succès !</div>";
                document.getElementById('formAjout').reset();
                // Supprimer le marker de la carte proprement
                if (marker) {
                    map.removeLayer(marker);
                    marker = null;
                }
            } else {
                document.getElementById('message').innerHTML =
                    "<div class='message error'> Erreur : " + (result.error || 'Erreur inconnue') + "</div>";
            }
        })
        .catch((err) => {
            console.error(err);
            document.getElementById('message').innerHTML =
                "<div class='message error'> Impossible de contacter le serveur.</div>";
        });
    });
</script>
</body>
</html>