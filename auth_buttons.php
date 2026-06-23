<?php
// Helper to display admin-related links depending on session.
// Include this in public pages where you want admin controls to appear/hidden.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['admin']) && $_SESSION['admin'] === true) {
    echo '<nav style="padding:8px 0;">';
    echo '<a href="admin.php" style="margin-right:10px;">Administration</a>';
    echo '<a href="modifier_ecole.php" style="margin-right:10px;">Modifier</a>';
    echo '<a href="supprimer_ecole.php" style="margin-right:10px;">Supprimer</a>';
    echo '<a href="logout.php" style="color:#c62828;">Se déconnecter</a>';
    echo '</nav>';
} else {
    echo '<nav style="padding:8px 0;">';
    echo '<a href="login.php">Connexion admin</a>';
    echo '</nav>';
}

?>