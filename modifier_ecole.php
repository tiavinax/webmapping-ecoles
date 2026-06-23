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
    <title>Admin — Modifier une école</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: #f0f2f5; }

        nav {
            background: #1565c0;
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

        .container { display: flex; height: calc(100vh - 50px); }
        #map { flex: 1; height: 100%; }

        .panel {
            width: 370px;
            background: white;
            padding: 20px;
            overflow-y: auto;
            box-shadow: -2px 0 8px rgba(0,0,0,0.1);
        }
        .panel h2 { color: #1565c0; margin-bottom: 15px; font-size: 16px; }

        /* RECHERCHE */
        .search-box {
            display: flex;
            gap: 8px;
            margin-bottom: 15px;
        }
        .search-box input {
            flex: 1;
            padding: 8px 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }
        .search-box button {
            padding: 8px 14px;
            background: #1565c0;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        /* LISTE ÉCOLES */
        #liste-ecoles { margin-bottom: 15px; }
        .ecole-item {
            padding: 10px;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            margin-bottom: 6px;
            cursor: pointer;
            transition: background 0.2s;
        }
        .ecole-item:hover { background: #e3f2fd; border-color: #1565c0; }
        .ecole-item.selected { background: #bbdefb; border-color: #1565c0; }
        .ecole-item .nom { font-weight: bold; font-size: 14px; }
        .ecole-item .detail { font-size: 12px; color: #666; margin-top: 2px; }

        /* FORMULAIRE */
        #form-modifier { display: none; }
        .form-titre {
            background: #e3f2fd;
            border-left: 4px solid #1565c0;
            padding: 10px;
            margin-bottom: 15px;
            font-size: 13px;
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
        input:focus, select:focus { border-color: #1565c0; outline: none; }

        .coord-box { display: flex; gap: 8px; }
        .coord-box input { background: #f9f9f9; color: #555; }

        .btn-modifier {
            width: 100%;
            margin-top: 20px;
            padding: 11px;
            background: #1565c0;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            cursor: pointer;
        }
        .btn-modifier:hover { background: #0d47a1; }
        .btn-annuler {
            width: 100%;
            margin-top: 8px;
            padding: 9px;
            background: #eee;
            color: #333;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
        }
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
    <h1> Admin — Modifier une école</h1>
    <a href="logout.php">Se déconnecter</a>
</nav>

<div class="container">
    <div id="map"></div>

    <div class="panel">
        <h2> Modifier une école</h2>

        <!-- RECHERCHE -->
        <div class="search-box">
            <input type="text" id="recherche" placeholder="Rechercher une école...">
            <button type="button" id="btn-recherche">🔍</button>
        </div>

        <!-- LISTE -->
        <div id="liste-ecoles">
            <p style="color:#999; font-size:13px;">Chargement des écoles...</p>
        </div>

        <!-- FORMULAIRE MODIFICATION -->
        <div id="form-modifier">
            <div class="form-titre">
                 Modification de : <strong id="titre-ecole"></strong><br>
                <small>Tu peux aussi déplacer le marker sur la carte.</small>
            </div>

            <input type="hidden" id="edit-id">

            <label>Nom de l'école *</label>
            <input type="text" id="edit-nom" required>

            <label>Type *</label>
            <select id="edit-type">
                <option value="primaire">Primaire</option>
                <option value="collège">Collège</option>
                <option value="lycée">Lycée</option>
            </select>

            <label>Statut *</label>
            <select id="edit-statut">
                <option value="public">Public</option>
                <option value="privé">Privé</option>
            </select>

            <label>Fokontany</label>
            <input type="text" id="edit-fokontany">

            <label>Téléphone</label>
            <input type="text" id="edit-telephone">

            <label>Nombre d'élèves</label>
            <input type="number" id="edit-nb_eleves">

            <label>Coordonnées (déplace le marker pour changer)</label>
            <div class="coord-box">
                <input type="text" id="edit-latitude"  placeholder="Latitude"  readonly>
                <input type="text" id="edit-longitude" placeholder="Longitude" readonly>
            </div>

            <button class="btn-modifier" onclick="enregistrerModification()"> Enregistrer les modifications</button>
            <button class="btn-annuler" onclick="annuler()"> Annuler</button>

            <div id="message"></div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const map = L.map('map').setView([-18.9500, 47.5400], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    let ecoles = [];
    let markerEdition = null;

    // Charger les écoles depuis l'API
    function chargerEcoles() {
        fetch('get_ecoles.php')
            .then(res => res.json())
            .then(data => {
                ecoles = data;
                afficherListe(ecoles);
                afficherMarkersCartes(ecoles);
            })
            .catch(() => {
                // Données de test si API pas prête
                ecoles = [
                    { id: 1, nom: "EPP Andoharanofotsy", type: "primaire", statut: "public",  fokontany: "Andoharanofotsy I", telephone: "034 00 000 01", nb_eleves: 320, latitude: -18.950, longitude: 47.540 },
                    { id: 2, nom: "Collège Saint-Joseph", type: "collège",  statut: "privé",   fokontany: "Andoharanofotsy II", telephone: "034 00 000 02", nb_eleves: 480, latitude: -18.952, longitude: 47.543 },
                    { id: 3, nom: "Lycée Andoharanofotsy", type: "lycée",  statut: "public",  fokontany: "Andoharanofotsy", telephone: "034 00 000 03", nb_eleves: 600, latitude: -18.948, longitude: 47.537 }
                ];
                afficherListe(ecoles);
                afficherMarkersCartes(ecoles);
            });
    }

    function afficherListe(liste) {
        const div = document.getElementById('liste-ecoles');
        if (liste.length === 0) {
            div.innerHTML = "<p style='color:#999;font-size:13px;'>Aucune école trouvée.</p>";
            return;
        }
        div.innerHTML = liste.map(e => `
            <div class="ecole-item" onclick="selectionnerEcole(${e.id})" id="item-${e.id}">
                <div class="nom">${e.nom}</div>
                <div class="detail">${e.type} • ${e.statut} • ${e.fokontany || ''}</div>
            </div>
        `).join('');
    }

    function afficherMarkersCartes(liste) {
        liste.forEach(e => {
            L.marker([e.latitude, e.longitude])
                .addTo(map)
                .bindPopup(`<b>${e.nom}</b><br>${e.type} - ${e.statut}`)
                .on('click', () => selectionnerEcole(e.id));
        });
    }

    function filtrerEcoles() {
        const q = (document.getElementById('recherche').value || '').toLowerCase();
        console.log('Recherche:', q);
        const listeResult = ecoles.filter(e =>
            (e.nom        || '').toLowerCase().includes(q) ||
            (e.type       || '').toLowerCase().includes(q) ||
            (e.fokontany  || '').toLowerCase().includes(q)
        );

        // Afficher message si la recherche est en cours ou vide
        const div = document.getElementById('liste-ecoles');
        if (q.trim() === '') {
            afficherListe(ecoles);
            return;
        }

        if (listeResult.length === 0) {
            div.innerHTML = "<p style='color:#999;font-size:13px;'>Aucune école trouvée pour '"+q+"'.</p>";
            return;
        }

        afficherListe(listeResult);
    }

    document.getElementById('recherche').addEventListener('input', filtrerEcoles);
    const btn = document.getElementById('btn-recherche');
    if (btn) btn.addEventListener('click', filtrerEcoles);

    function selectionnerEcole(id) {
        const e = ecoles.find(x => x.id === id);
        if (!e) return;

        // Surligner dans la liste
        document.querySelectorAll('.ecole-item').forEach(el => el.classList.remove('selected'));
        const item = document.getElementById('item-' + id);
        if (item) item.classList.add('selected');

        // Remplir le formulaire
        document.getElementById('edit-id').value        = e.id;
        document.getElementById('edit-nom').value       = e.nom;
        document.getElementById('edit-type').value      = e.type;
        document.getElementById('edit-statut').value    = e.statut;
        document.getElementById('edit-fokontany').value = e.fokontany || '';
        document.getElementById('edit-telephone').value = e.telephone || '';
        document.getElementById('edit-nb_eleves').value = e.nb_eleves || '';
        document.getElementById('edit-latitude').value  = e.latitude;
        document.getElementById('edit-longitude').value = e.longitude;
        document.getElementById('titre-ecole').textContent = e.nom;
        document.getElementById('message').innerHTML = '';

        // Marker déplaçable sur la carte
        if (markerEdition) map.removeLayer(markerEdition);
        markerEdition = L.marker([e.latitude, e.longitude], { draggable: true }).addTo(map);
        map.setView([e.latitude, e.longitude], 16);

        markerEdition.on('dragend', function() {
            const pos = markerEdition.getLatLng();
            document.getElementById('edit-latitude').value  = pos.lat.toFixed(6);
            document.getElementById('edit-longitude').value = pos.lng.toFixed(6);
        });

        // Afficher le formulaire
        document.getElementById('form-modifier').style.display = 'block';
    }

    function enregistrerModification() {
        const data = {
            id:        document.getElementById('edit-id').value,
            nom:       document.getElementById('edit-nom').value,
            type:      document.getElementById('edit-type').value,
            statut:    document.getElementById('edit-statut').value,
            fokontany: document.getElementById('edit-fokontany').value,
            telephone: document.getElementById('edit-telephone').value,
            nb_eleves: document.getElementById('edit-nb_eleves').value,
            latitude:  document.getElementById('edit-latitude').value,
            longitude: document.getElementById('edit-longitude').value
        };

        fetch('update_ecole.php', {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(res => res.text().then(text => {
            try { return text ? JSON.parse(text) : { success: false, error: 'Réponse vide du serveur' }; }
            catch (e) { return { success: false, error: text || 'Réponse non valide du serveur' }; }
        }))
        .then(result => {
            if (result.success) {
                document.getElementById('message').innerHTML =
                    "<div class='message success'> École modifiée avec succès !</div>";

                // Mettre à jour la liste locale pour refléter les changements
                const idx = ecoles.findIndex(x => x.id === parseInt(data.id));
                if (idx !== -1) {
                    ecoles[idx] = Object.assign(ecoles[idx], {
                        nom:       data.nom,
                        type:      data.type,
                        statut:    data.statut,
                        fokontany: data.fokontany,
                        telephone: data.telephone,
                        nb_eleves: data.nb_eleves,
                        latitude:  parseFloat(data.latitude),
                        longitude: parseFloat(data.longitude)
                    });
                    // Mettre à jour le titre affiché
                    document.getElementById('titre-ecole').textContent = data.nom;
                    // Rafraîchir la liste HTML
                    afficherListe(ecoles);
                    // Surligner à nouveau l'élément modifié
                    const item = document.getElementById('item-' + data.id);
                    if (item) item.classList.add('selected');
                }
            } else {
                document.getElementById('message').innerHTML =
                    "<div class='message error'> Erreur : " + result.error + "</div>";
            }
        })
        .catch(() => {
            document.getElementById('message').innerHTML =
                "<div class='message error'> Impossible de contacter le serveur.</div>";
        });
    }

    function annuler() {
        document.getElementById('form-modifier').style.display = 'none';
        if (markerEdition) { map.removeLayer(markerEdition); markerEdition = null; }
        document.querySelectorAll('.ecole-item').forEach(el => el.classList.remove('selected'));
    }

    chargerEcoles();
</script>
</body>
</html>