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
    <title>Admin — Supprimer une école</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: #f0f2f5; }

        nav {
            background: #b71c1c;
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
            background: rgba(0,0,0,0.3);
            padding: 6px 14px;
            border-radius: 5px;
            font-size: 14px;
        }
        nav a:hover { background: rgba(0,0,0,0.5); }

        .container { display: flex; height: calc(100vh - 50px); }
        #map { flex: 1; height: 100%; }

        .panel {
            width: 350px;
            background: white;
            padding: 20px;
            overflow-y: auto;
            box-shadow: -2px 0 8px rgba(0,0,0,0.1);
        }
        .panel h2 { color: #b71c1c; margin-bottom: 15px; font-size: 16px; }

        .search-box { display: flex; gap: 8px; margin-bottom: 15px; }
        .search-box input {
            flex: 1; padding: 8px 10px;
            border: 1px solid #ccc; border-radius: 5px; font-size: 14px;
        }
        .search-box button {
            padding: 8px 14px; background: #b71c1c;
            color: white; border: none; border-radius: 5px; cursor: pointer;
        }
        .search-box button:hover { background: #7f0000; }

        .ecole-item {
            padding: 10px; border: 1px solid #e0e0e0;
            border-radius: 5px; margin-bottom: 8px;
            display: flex; justify-content: space-between; align-items: center;
        }
        .ecole-item:hover { background: #fff8f8; border-color: #b71c1c; }
        .ecole-info .nom { font-weight: bold; font-size: 14px; }
        .ecole-info .detail { font-size: 12px; color: #666; margin-top: 2px; }
        .btn-suppr {
            background: #b71c1c; color: white;
            border: none; border-radius: 5px;
            padding: 7px 12px; cursor: pointer; font-size: 13px;
            white-space: nowrap;
        }
        .btn-suppr:hover { background: #7f0000; }

        /* MODAL CONFIRMATION */
        .modal-overlay {
            display: none;
            position: fixed; top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 9999;
            justify-content: center; align-items: center;
        }
        .modal-overlay.active { display: flex; }
        .modal {
            background: white;
            border-radius: 10px;
            padding: 30px;
            width: 360px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }
        .modal .icone { font-size: 48px; margin-bottom: 10px; }
        .modal h3 { color: #b71c1c; margin-bottom: 10px; }
        .modal p { color: #555; font-size: 14px; margin-bottom: 20px; }
        .modal .nom-ecole {
            background: #ffebee; color: #b71c1c;
            padding: 8px 14px; border-radius: 5px;
            font-weight: bold; margin-bottom: 20px;
            display: inline-block;
        }
        .modal-btns { display: flex; gap: 10px; justify-content: center; }
        .btn-confirmer {
            padding: 10px 24px; background: #b71c1c;
            color: white; border: none; border-radius: 6px;
            font-size: 15px; cursor: pointer;
        }
        .btn-confirmer:hover { background: #7f0000; }
        .btn-annuler-modal {
            padding: 10px 24px; background: #eee;
            color: #333; border: none; border-radius: 6px;
            font-size: 15px; cursor: pointer;
        }
        .btn-annuler-modal:hover { background: #ddd; }

        .message {
            margin-top: 12px; padding: 10px;
            border-radius: 5px; font-size: 13px; text-align: center;
        }
        .success { background: #e8f5e9; color: #2e7d32; }
        .error   { background: #ffebee; color: #c62828; }
    </style>
</head>
<body>

<nav>
    <h1> Admin — Supprimer une école</h1>
    <a href="logout.php">Se déconnecter</a>
</nav>

<div class="container">
    <div id="map"></div>

    <div class="panel">
        <h2> Supprimer une école</h2>

        <div class="search-box">
            <input type="text" id="recherche" placeholder="Rechercher une école...">
            <button type="button" id="btn-recherche">🔍 Rechercher</button>
        </div>

        <div id="liste-ecoles">
            <p style="color:#999;font-size:13px;">Chargement...</p>
        </div>

        <div id="message"></div>
    </div>
</div>

<!-- MODAL DE CONFIRMATION -->
<div class="modal-overlay" id="modal">
    <div class="modal">
        <div class="icone">⚠️</div>
        <h3>Confirmer la suppression</h3>
        <p>Tu es sur le point de supprimer définitivement :</p>
        <div class="nom-ecole" id="modal-nom-ecole"></div>
        <p style="color:#b71c1c; font-size:13px;">Cette action est irréversible !</p>
        <div class="modal-btns">
            <button class="btn-annuler-modal" onclick="fermerModal()"> Annuler</button>
            <button class="btn-confirmer" onclick="confirmerSuppression()"> Supprimer</button>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // ===================== INITIALISATION DE LA CARTE =====================
    const map = L.map('map').setView([-18.9500, 47.5400], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    let ecoles = [];
    let idASupprimer = null;
    let markers = {};

    // ===================== CHARGEMENT DES ÉCOLES =====================
    function chargerEcoles() {
        fetch('get_ecoles.php')
            .then(res => res.json())
            .then(data => {
                ecoles = data;
                afficherListe(ecoles);
                afficherMarkers(ecoles);
            })
            .catch(() => {
                // Données de test
                ecoles = [
                    { id: 1, nom: "EPP Andoharanofotsy I", type: "primaire", statut: "public", fokontany: "Andoharanofotsy I",  latitude: -18.950, longitude: 47.540 },
                    { id: 2, nom: "EPP Andoharanofotsy II", type: "primaire", statut: "public", fokontany: "Andoharanofotsy II", latitude: -18.951, longitude: 47.542 },
                    { id: 3, nom: "Collège Saint-Joseph",   type: "collège",  statut: "privé",  fokontany: "Andoharanofotsy II", latitude: -18.952, longitude: 47.543 },
                    { id: 4, nom: "Lycée Andoharanofotsy",  type: "lycée",    statut: "public", fokontany: "Andoharanofotsy",    latitude: -18.948, longitude: 47.537 }
                ];
                afficherListe(ecoles);
                afficherMarkers(ecoles);
            });
    }

    // ===================== AFFICHAGE DE LA LISTE =====================
    function afficherListe(liste) {
        const div = document.getElementById('liste-ecoles');
        if (liste.length === 0) {
            div.innerHTML = "<p style='color:#999;font-size:13px;'>Aucune école trouvée.</p>";
            return;
        }
        div.innerHTML = liste.map(e => `
            <div class="ecole-item" id="item-${e.id}">
                <div class="ecole-info">
                    <div class="nom">${e.nom}</div>
                    <div class="detail">${e.type} • ${e.statut} • ${e.fokontany || ''}</div>
                </div>
                <button class="btn-suppr" onclick="ouvrirModal(${e.id})">🗑️ Supprimer</button>
            </div>
        `).join('');
    }

    // ===================== AFFICHAGE DES MARKERS =====================
    function afficherMarkers(liste) {
        // Supprimer les anciens markers
        Object.values(markers).forEach(m => map.removeLayer(m));
        markers = {};

        liste.forEach(e => {
            const m = L.marker([e.latitude, e.longitude])
                .addTo(map)
                .bindPopup(`
                    <b>${e.nom}</b><br>
                    ${e.type} - ${e.statut}<br>
                    <button onclick="ouvrirModal(${e.id})"
                        style="margin-top:6px;background:#b71c1c;color:white;border:none;
                               padding:5px 10px;border-radius:4px;cursor:pointer;">
                         Supprimer
                    </button>
                `);
            markers[e.id] = m;
        });
    }

    // ===================== FONCTION DE RECHERCHE (GLOBALE) =====================
    window.filtrerEcoles = function() {
        const q = document.getElementById('recherche').value.toLowerCase().trim();
        if (q === '') {
            afficherListe(ecoles);
            return;
        }
        const filtre = ecoles.filter(e => 
            e.nom.toLowerCase().includes(q) ||
            (e.fokontany && e.fokontany.toLowerCase().includes(q))
        );
        afficherListe(filtre);
    };

    // ===================== ÉCOUTEURS D'ÉVÉNEMENTS =====================
    document.getElementById('recherche').addEventListener('input', window.filtrerEcoles);
    document.getElementById('btn-recherche').addEventListener('click', window.filtrerEcoles);

    // Touche Entrée dans le champ de recherche
    document.getElementById('recherche').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            window.filtrerEcoles();
        }
    });

    // ===================== OUVERTURE DU MODAL =====================
    window.ouvrirModal = function(id) {
        const e = ecoles.find(x => x.id === id);
        if (!e) {
            document.getElementById('message').innerHTML =
                "<div class='message error'> École introuvable.</div>";
            return;
        }
        idASupprimer = id;
        document.getElementById('modal-nom-ecole').textContent = e.nom;
        document.getElementById('modal').classList.add('active');

        // Centrer la carte sur l'école
        map.setView([e.latitude, e.longitude], 16);
        if (markers[id]) markers[id].openPopup();
    };

    // ===================== FERMETURE DU MODAL =====================
    window.fermerModal = function() {
        document.getElementById('modal').classList.remove('active');
        idASupprimer = null;
    };

    // Fermer modal si clic en dehors
    document.getElementById('modal').addEventListener('click', function(e) {
        if (e.target === this) window.fermerModal();
    });

    // ===================== CONFIRMATION DE SUPPRESSION =====================
    window.confirmerSuppression = function() {
        if (!idASupprimer) {
            window.fermerModal();
            return;
        }
        console.log('confirmerSuppression called, idASupprimer=', idASupprimer);

        // Désactiver le bouton pour éviter les doubles clics
        const btnConfirmer = document.querySelector('.btn-confirmer');
        btnConfirmer.disabled = true;
        btnConfirmer.textContent = 'Suppression...';
        const payload = { id: idASupprimer };
        console.log('Envoi fetch delete_ecole.php', payload);

        fetch('delete_ecole.php', {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(res => res.text().then(text => {
            console.log('Réponse brute delete_ecole.php:', text);
            try { return text ? JSON.parse(text) : { success: false, error: 'Réponse vide' }; }
            catch (e) { return { success: false, error: 'Réponse JSON invalide' }; }
        }))
        .then(result => {
            console.log('Result delete:', result);
            window.fermerModal();
            btnConfirmer.disabled = false;
            btnConfirmer.textContent = 'Supprimer';

            if (result.success) {
                // Trouver le nom de l'école supprimée
                const ecoleSupprimee = ecoles.find(e => e.id === idASupprimer);
                const nom = ecoleSupprimee ? ecoleSupprimee.nom : 'École';

                // Supprimer de la liste locale
                ecoles = ecoles.filter(e => e.id !== idASupprimer);

                // Supprimer le marker de la carte
                if (markers[idASupprimer]) {
                    map.removeLayer(markers[idASupprimer]);
                    delete markers[idASupprimer];
                }

                // Rafraîchir la liste
                afficherListe(ecoles);

                document.getElementById('message').innerHTML =
                    `<div class='message success'> "${nom}" supprimée avec succès !</div>`;

                // Effacer le message après 5 secondes
                setTimeout(() => {
                    document.getElementById('message').innerHTML = '';
                }, 5000);
            } else {
                document.getElementById('message').innerHTML =
                    `<div class='message error'> Erreur : ${result.error || 'Erreur inconnue'}</div>`;
            }
        })
        .catch((error) => {
            window.fermerModal();
            btnConfirmer.disabled = false;
            btnConfirmer.textContent = 'Supprimer';
            console.error('Erreur:', error);
            document.getElementById('message').innerHTML =
                "<div class='message error'> Impossible de contacter le serveur.</div>";
        });
    };

    // ===================== LANCER LE CHARGEMENT =====================
    chargerEcoles();
</script>
</body>
</html>