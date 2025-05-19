<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST["nom"];
    $prenom = $_POST["prenom"];
    $email = $_POST["email"];
    $telephone = $_POST["telephone"];
    $experience = $_POST["experience"];
    $zone = $_POST["zone"];
    $vehicule = $_POST["vehicule"];
    $date = $_POST["date"];

    $ligne = [$nom, $prenom, $email, $telephone, $experience, $zone, $vehicule, $date];
    $fichier = fopen("candidatures.csv", "a");
    fputcsv($fichier, $ligne);
    fclose($fichier);

    header("Location: admin.html");
    exit;
}
?>
