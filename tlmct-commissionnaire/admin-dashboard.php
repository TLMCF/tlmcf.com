<?php
session_start();
if (!isset($_SESSION["admin"])) {
    header("Location: admin-login.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord Admin</title>
    <style>
        body { font-family: Arial; background-color: #f8f8f8; padding: 40px; }
        h1 { color: #333; }
        a { display: block; margin-top: 15px; font-size: 18px; color: #0066cc; text-decoration: none; }
        a:hover { text-decoration: underline; }
        .logout { margin-top: 30px; color: red; }
    </style>
</head>
<body>

<h1>Bienvenue sur le Tableau de Bord Admin</h1>

<a href="admin-candidatures.html">📄 Voir les candidatures transporteurs</a>
<a href="admin-demandes.php">🚛 Voir les demandes de transport</a>

<a href="logout.php" class="logout">🔐 Se déconnecter</a>

</body>
</html>
