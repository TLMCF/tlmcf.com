<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = strip_tags(trim($_POST["nom"]));
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $telephone = strip_tags(trim($_POST["telephone"]));
    $message = strip_tags(trim($_POST["message"]));

    $destinataire = "contact@tonsite.com"; // Ton email chez IONOS
    $sujet = "Nouveau message depuis votre site web";

    $contenu = "Nom et Prénom: $nom\n";
    $contenu .= "Email: $email\n";
    $contenu .= "Téléphone: $telephone\n\n";
    $contenu .= "Message:\n$message\n";

    $headers = "From: $nom <$email>";

    if (mail($destinataire, $sujet, $contenu, $headers)) {
        echo "Message envoyé avec succès !";
    } else {
        echo "Erreur : Le message n'a pas pu être envoyé.";
    }
} else {
    echo "Erreur dans l'envoi du formulaire.";
}
?>
