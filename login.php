<?php
session_start();

$mot_de_passe_admin = "admin123"; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['password'] === $mot_de_passe_admin) {
        $_SESSION['admin'] = true;
        header('Location: admin.php');
        exit;
    } else {
        $erreur = "Mot de passe incorrect !";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f0f2f5;
            margin: 0;
        }
        .login-box {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            width: 300px;
            text-align: center;
        }
        h2 { color: #333; margin-bottom: 20px; }
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #2e7d32;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover { background-color: #1b5e20; }
        .erreur { color: red; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2> Admin Écoles</h2>
        <p>Andoharanofotsy</p>
        <form method="POST">
            <input type="password" name="password" placeholder="Mot de passe" required>
            <button type="submit">Se connecter</button>
        </form>
        <?php if (isset($erreur)) echo "<p class='erreur'>$erreur</p>"; ?>
    </div>
</body>
</html>