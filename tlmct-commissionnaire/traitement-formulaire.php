<?php
// Définir l'encodage des caractères
header('Content-Type: text/html; charset=utf-8');

// --- Configuration ---
// L'adresse email où vous souhaitez recevoir les demandes
$destinataire_email = 'martinez.frederic33@outlook.fr'; // REMPLACEZ CECI PAR VOTRE ADRESSE EMAIL
$sujet_email = 'Nouvelle demande de devis transport';
$url_page_succes = 'merci.html'; // Page vers laquelle rediriger après succès
$url_page_erreur = 'erreur.html'; // Page vers laquelle rediriger en cas d'erreur

// --- Vérification de la méthode de requête ---
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    // Si la requête n'est pas en POST, rediriger ou afficher une erreur
    header("Location: " . $url_page_erreur . "?error=method_not_allowed");
    exit;
}

// --- Récupération et Nettoyage des Données ---
// Utilisez filter_input pour récupérer et nettoyer les données POST
// FILTER_SANITIZE_STRING est obsolète, utilisez FILTER_UNSAFE_RAW et htmlspecialchars
// Assurez-vous que les champs requis sont présents

$erreurs = []; // Tableau pour stocker les erreurs de validation

$societe = filter_input(INPUT_POST, 'societe', FILTER_UNSAFE_RAW);
$societe = htmlspecialchars($societe ?? '', ENT_QUOTES, 'UTF-8');

$nom_contact = filter_input(INPUT_POST, 'nom_contact', FILTER_UNSAFE_RAW);
$nom_contact = htmlspecialchars($nom_contact ?? '', ENT_QUOTES, 'UTF-8');
if (empty($nom_contact)) {
    $erreurs[] = "Le nom du contact est obligatoire.";
}

$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
if (!$email) {
    $erreurs[] = "L'adresse email n'est pas valide.";
}

$telephone = filter_input(INPUT_POST, 'telephone', FILTER_UNSAFE_RAW);
$telephone = htmlspecialchars($telephone ?? '', ENT_QUOTES, 'UTF-8');
if (empty($telephone)) {
     $erreurs[] = "Le numéro de téléphone est obligatoire.";
}

$siret_tva = filter_input(INPUT_POST, 'siret_tva', FILTER_UNSAFE_RAW);
$siret_tva = htmlspecialchars($siret_tva ?? '', ENT_QUOTES, 'UTF-8');

$mode_transport = filter_input(INPUT_POST, 'mode_transport', FILTER_UNSAFE_RAW);
$mode_transport = htmlspecialchars($mode_transport ?? '', ENT_QUOTES, 'UTF-8');
if (empty($mode_transport)) {
     $erreurs[] = "Le mode de transport est obligatoire.";
}

$incoterm = filter_input(INPUT_POST, 'incoterm', FILTER_UNSAFE_RAW);
$incoterm = htmlspecialchars($incoterm ?? '', ENT_QUOTES, 'UTF-8');

$origine_adresse = filter_input(INPUT_POST, 'origine_adresse', FILTER_UNSAFE_RAW);
$origine_adresse = htmlspecialchars($origine_adresse ?? '', ENT_QUOTES, 'UTF-8');
if (empty($origine_adresse)) {
    $erreurs[] = "L'adresse d'enlèvement est obligatoire.";
}

$origine_cp = filter_input(INPUT_POST, 'origine_cp', FILTER_UNSAFE_RAW);
$origine_cp = htmlspecialchars($origine_cp ?? '', ENT_QUOTES, 'UTF-8');
if (empty($origine_cp)) {
    $erreurs[] = "Le code postal d'enlèvement est obligatoire.";
}

$origine_ville = filter_input(INPUT_POST, 'origine_ville', FILTER_UNSAFE_RAW);
$origine_ville = htmlspecialchars($origine_ville ?? '', ENT_QUOTES, 'UTF-8');
if (empty($origine_ville)) {
    $erreurs[] = "La ville d'enlèvement est obligatoire.";
}

$origine_pays = filter_input(INPUT_POST, 'origine_pays', FILTER_UNSAFE_RAW);
$origine_pays = htmlspecialchars($origine_pays ?? '', ENT_QUOTES, 'UTF-8');
if (empty($origine_pays)) {
    $erreurs[] = "Le pays d'enlèvement est obligatoire.";
}

$origine_instructions = filter_input(INPUT_POST, 'origine_instructions', FILTER_UNSAFE_RAW);
$origine_instructions = htmlspecialchars($origine_instructions ?? '', ENT_QUOTES, 'UTF-8');

$destination_adresse = filter_input(INPUT_POST, 'destination_adresse', FILTER_UNSAFE_RAW);
$destination_adresse = htmlspecialchars($destination_adresse ?? '', ENT_QUOTES, 'UTF-8');
if (empty($destination_adresse)) {
    $erreurs[] = "L'adresse de livraison est obligatoire.";
}

$destination_cp = filter_input(INPUT_POST, 'destination_cp', FILTER_UNSAFE_RAW);
$destination_cp = htmlspecialchars($destination_cp ?? '', ENT_QUOTES, 'UTF-8');
if (empty($destination_cp)) {
    $erreurs[] = "Le code postal de livraison est obligatoire.";
}

$destination_ville = filter_input(INPUT_POST, 'destination_ville', FILTER_UNSAFE_RAW);
$destination_ville = htmlspecialchars($destination_ville ?? '', ENT_QUOTES, 'UTF-8');
if (empty($destination_ville)) {
    $erreurs[] = "La ville de livraison est obligatoire.";
}

$destination_pays = filter_input(INPUT_POST, 'destination_pays', FILTER_UNSAFE_RAW);
$destination_pays = htmlspecialchars($destination_pays ?? '', ENT_QUOTES, 'UTF-8');
if (empty($destination_pays)) {
    $erreurs[] = "Le pays de livraison est obligatoire.";
}

$destination_instructions = filter_input(INPUT_POST, 'destination_instructions', FILTER_UNSAFE_RAW);
$destination_instructions = htmlspecialchars($destination_instructions ?? '', ENT_QUOTES, 'UTF-8');

$nature_marchandise = filter_input(INPUT_POST, 'nature_marchandise', FILTER_UNSAFE_RAW);
$nature_marchandise = htmlspecialchars($nature_marchandise ?? '', ENT_QUOTES, 'UTF-8');
if (empty($nature_marchandise)) {
    $erreurs[] = "La nature de la marchandise est obligatoire.";
}

$code_douanier_hs = filter_input(INPUT_POST, 'code_douanier_hs', FILTER_UNSAFE_RAW);
$code_douanier_hs = htmlspecialchars($code_douanier_hs ?? '', ENT_QUOTES, 'UTF-8');

// Récupérer les données du premier colis (vous devrez étendre ceci si vous avez plusieurs blocs colis)
$type_emballage1 = filter_input(INPUT_POST, 'type_emballage1', FILTER_UNSAFE_RAW);
$type_emballage1 = htmlspecialchars($type_emballage1 ?? '', ENT_QUOTES, 'UTF-8');
if (empty($type_emballage1)) {
     $erreurs[] = "Le type d'emballage est obligatoire.";
}
$autre_emballage_details1 = filter_input(INPUT_POST, 'autre_emballage_details1', FILTER_UNSAFE_RAW);
$autre_emballage_details1 = htmlspecialchars($autre_emballage_details1 ?? '', ENT_QUOTES, 'UTF-8');

$nombre_colis1 = filter_input(INPUT_POST, 'nombre_colis1', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if ($nombre_colis1 === false || $nombre_colis1 === null) {
    $erreurs[] = "Le nombre de colis doit être un nombre entier positif.";
}

$longueur_colis1 = filter_input(INPUT_POST, 'longueur_colis1', FILTER_VALIDATE_FLOAT);
$largeur_colis1 = filter_input(INPUT_POST, 'largeur_colis1', FILTER_VALIDATE_FLOAT);
$hauteur_colis1 = filter_input(INPUT_POST, 'hauteur_colis1', FILTER_VALIDATE_FLOAT);

$poids_colis1 = filter_input(INPUT_POST, 'poids_colis1', FILTER_VALIDATE_FLOAT, ['options' => ['min_range' => 0]]);
if ($poids_colis1 === false || $poids_colis1 === null) {
    $erreurs[] = "Le poids par colis doit être un nombre positif.";
}

$colis_gerbable1 = filter_input(INPUT_POST, 'colis_gerbable1', FILTER_UNSAFE_RAW);
$colis_gerbable1 = htmlspecialchars($colis_gerbable1 ?? '', ENT_QUOTES, 'UTF-8');
$gerbable_details1 = filter_input(INPUT_POST, 'gerbable_details1', FILTER_UNSAFE_RAW);
$gerbable_details1 = htmlspecialchars($gerbable_details1 ?? '', ENT_QUOTES, 'UTF-8');


$poids_total = filter_input(INPUT_POST, 'poids_total', FILTER_VALIDATE_FLOAT, ['options' => ['min_range' => 0]]);
if ($poids_total === false || $poids_total === null) {
    $erreurs[] = "Le poids total doit être un nombre positif.";
}

$volume_total = filter_input(INPUT_POST, 'volume_total', FILTER_VALIDATE_FLOAT, ['options' => ['min_range' => 0]]);

$valeur_marchandise = filter_input(INPUT_POST, 'valeur_marchandise', FILTER_VALIDATE_FLOAT, ['options' => ['min_range' => 0]]);
$devise_valeur = filter_input(INPUT_POST, 'devise_valeur', FILTER_UNSAFE_RAW);
$devise_valeur = htmlspecialchars($devise_valeur ?? '', ENT_QUOTES, 'UTF-8');

$assurance_ad_valorem = filter_input(INPUT_POST, 'assurance_ad_valorem', FILTER_UNSAFE_RAW);
$assurance_ad_valorem = htmlspecialchars($assurance_ad_valorem ?? '', ENT_QUOTES, 'UTF-8');
if (empty($assurance_ad_valorem)) {
    $erreurs[] = "L'option d'assurance Ad Valorem est obligatoire.";
}

// Récupérer les spécificités (checkboxes)
$specificites = filter_input(INPUT_POST, 'specificites', FILTER_UNSAFE_RAW, FILTER_REQUIRE_ARRAY);
$specificites = is_array($specificites) ? array_map(function($item) { return htmlspecialchars($item, ENT_QUOTES, 'UTF-8'); }, $specificites) : [];

// Récupérer les détails si dangereux coché
$classe_onu = '';
$fiche_securite = '';
if (in_array('dangereuse', $specificites)) {
    $classe_onu = filter_input(INPUT_POST, 'classe_onu', FILTER_UNSAFE_RAW);
    $classe_onu = htmlspecialchars($classe_onu ?? '', ENT_QUOTES, 'UTF-8');
    $fiche_securite = filter_input(INPUT_POST, 'fiche_securite', FILTER_UNSAFE_RAW);
    $fiche_securite = htmlspecialchars($fiche_securite ?? '', ENT_QUOTES, 'UTF-8');
}

// Récupérer les détails si température dirigée coché
$temperature_requise = '';
if (in_array('temperature_dirigee', $specificites)) {
    $temperature_requise = filter_input(INPUT_POST, 'temperature_requise', FILTER_UNSAFE_RAW);
    $temperature_requise = htmlspecialchars($temperature_requise ?? '', ENT_QUOTES, 'UTF-8');
}


$date_enlevement_souhaitee = filter_input(INPUT_POST, 'date_enlevement_souhaitee', FILTER_UNSAFE_RAW);
$date_enlevement_souhaitee = htmlspecialchars($date_enlevement_souhaitee ?? '', ENT_QUOTES, 'UTF-8');
if (empty($date_enlevement_souhaitee)) {
    $erreurs[] = "La date d'enlèvement souhaitée est obligatoire.";
}

$date_livraison_imperative = filter_input(INPUT_POST, 'date_livraison_imperative', FILTER_UNSAFE_RAW);
$date_livraison_imperative = htmlspecialchars($date_livraison_imperative ?? '', ENT_QUOTES, 'UTF-8');

// Récupérer les documents disponibles (checkboxes)
$documents = filter_input(INPUT_POST, 'documents', FILTER_UNSAFE_RAW, FILTER_REQUIRE_ARRAY);
$documents = is_array($documents) ? array_map(function($item) { return htmlspecialchars($item, ENT_QUOTES, 'UTF-8'); }, $documents) : [];


$budget_approx = filter_input(INPUT_POST, 'budget_approx', FILTER_UNSAFE_RAW);
$budget_approx = htmlspecialchars($budget_approx ?? '', ENT_QUOTES, 'UTF-8');

$commentaires = filter_input(INPUT_POST, 'commentaires', FILTER_UNSAFE_RAW);
$commentaires = htmlspecialchars($commentaires ?? '', ENT_QUOTES, 'UTF-8');

$reference_interne = filter_input(INPUT_POST, 'reference_interne', FILTER_UNSAFE_RAW);
$reference_interne = htmlspecialchars($reference_interne ?? '', ENT_QUOTES, 'UTF-8');

// Gérer le fichier joint
$fichier_joint_path = null;
if (isset($_FILES['fichier_joint']) && $_FILES['fichier_joint']['error'] == UPLOAD_ERR_OK) {
    $upload_dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR; // Utiliser un répertoire temporaire
    $upload_file = $upload_dir . basename($_FILES['fichier_joint']['name']);

    // Validation basique du fichier (type, taille) - À renforcer !
    $allowed_types = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'image/jpeg', 'image/png'];
    $max_size = 5 * 1024 * 1024; // 5 Mo

    if (in_array($_FILES['fichier_joint']['type'], $allowed_types) && $_FILES['fichier_joint']['size'] <= $max_size) {
        if (move_uploaded_file($_FILES['fichier_joint']['tmp_name'], $upload_file)) {
            $fichier_joint_path = $upload_file;
        } else {
            $erreurs[] = "Erreur lors du téléchargement du fichier joint.";
        }
    } else {
        $erreurs[] = "Type de fichier non autorisé ou taille maximale (5Mo) dépassée.";
    }
}


// Vérifier si la case "contrat accepté" est cochée
$contrat_accepte = isset($_POST['contrat']);
if (!$contrat_accepte) {
    $erreurs[] = "Vous devez accepter le contrat de transport.";
}


// --- Gestion des Erreurs de Validation ---
if (!empty($erreurs)) {
    // Rediriger vers la page d'erreur et potentiellement passer les messages d'erreur
    // ou les stocker en session pour les afficher
    // Pour cet exemple simple, on redirige juste
    header("Location: " . $url_page_erreur . "?errors=" . urlencode(implode(", ", $erreurs)));
    exit;
}

// --- Préparation des Données pour le PDF ---
// Rassembler toutes les données du formulaire dans un format facile à utiliser
$donnees_formulaire = [
    'societe' => $societe,
    'nom_contact' => $nom_contact,
    'email' => $email,
    'telephone' => $telephone,
    'siret_tva' => $siret_tva,
    'mode_transport' => $mode_transport,
    'incoterm' => $incoterm,
    'origine_adresse' => $origine_adresse,
    'origine_cp' => $origine_cp,
    'origine_ville' => $origine_ville,
    'origine_pays' => $origine_pays,
    'origine_instructions' => $origine_instructions,
    'destination_adresse' => $destination_adresse,
    'destination_cp' => $destination_cp,
    'destination_ville' => $destination_ville,
    'destination_pays' => $destination_pays,
    'destination_instructions' => $destination_instructions,
    'nature_marchandise' => $nature_marchandise,
    'code_douanier_hs' => $code_douanier_hs,
    'colis' => [ // Structure pour les colis (vous devrez adapter si plusieurs types)
        [
            'type_emballage' => $type_emballage1,
            'autre_emballage_details' => $autre_emballage_details1,
            'nombre' => $nombre_colis1,
            'longueur' => $longueur_colis1,
            'largeur' => $largeur_colis1,
            'hauteur' => $hauteur_colis1,
            'poids_unitaire' => $poids_colis1,
            'gerbable' => $colis_gerbable1,
            'gerbable_details' => $gerbable_details1,
        ]
    ],
    'poids_total' => $poids_total,
    'volume_total' => $volume_total,
    'valeur_marchandise' => $valeur_marchandise,
    'devise_valeur' => $devise_valeur,
    'assurance_ad_valorem' => $assurance_ad_valorem,
    'specificites' => $specificites,
    'classe_onu' => $classe_onu, // Détails si dangereux
    'fiche_securite' => $fiche_securite, // Détails si dangereux
    'temperature_requise' => $temperature_requise, // Détails si température
    'date_enlevement_souhaitee' => $date_enlevement_souhaitee,
    'date_livraison_imperative' => $date_livraison_imperative,
    'documents' => $documents,
    'budget_approx' => $budget_approx,
    'commentaires' => $commentaires,
    'reference_interne' => $reference_interne,
    'contrat_accepte' => $contrat_accepte ? 'Oui' : 'Non', // Pour le PDF/Email
];


// --- Génération du Contenu HTML pour le PDF (en utilisant Dompdf) ---
// Cette partie est cruciale et doit être adaptée pour ressembler à une lettre de voiture.
// Vous construisez une chaîne HTML qui sera convertie en PDF.

$html_content = '
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Demande de Devis - ' . htmlspecialchars($donnees_formulaire['nom_contact']) . '</title>
    <style>
        body { font-family: sans-serif; margin: 20mm; font-size: 10pt; }
        h1, h2 { color: #0056b3; border-bottom: 1px solid #eee; padding-bottom: 5px; margin-top: 20px; }
        fieldset { margin-bottom: 20px; padding: 15px; border: 1px solid #ccc; }
        legend { font-weight: bold; color: #0056b3; padding: 0 10px; }
        p { margin-bottom: 10px; }
        strong { display: inline-block; width: 180px; } /* Ajustez la largeur si nécessaire */
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .section-title { font-weight: bold; margin-top: 15px; margin-bottom: 5px; }
    </style>
</head>
<body>

    <h1>Demande de Devis Transport</h1>
    <p>Référence interne : ' . htmlspecialchars($donnees_formulaire['reference_interne']) . '</p>

    <fieldset>
        <legend>Informations de Contact</legend>
        <p><strong>Société :</strong> ' . htmlspecialchars($donnees_formulaire['societe']) . '</p>
        <p><strong>Nom du contact :</strong> ' . htmlspecialchars($donnees_formulaire['nom_contact']) . '</p>
        <p><strong>Email :</strong> ' . htmlspecialchars($donnees_formulaire['email']) . '</p>
        <p><strong>Téléphone :</strong> ' . htmlspecialchars($donnees_formulaire['telephone']) . '</p>
        <p><strong>N° SIRET / TVA :</strong> ' . htmlspecialchars($donnees_formulaire['siret_tva']) . '</p>
    </fieldset>

    <fieldset>
        <legend>Détails de l\'Expédition</legend>
        <p><strong>Mode de transport souhaité :</strong> ' . htmlspecialchars($donnees_formulaire['mode_transport']) . '</p>
        <p><strong>Incoterm :</strong> ' . htmlspecialchars($donnees_formulaire['incoterm']) . '</p>
    </fieldset>

    <fieldset>
        <legend>Lieu d\'Enlèvement (Origine)</legend>
        <p><strong>Adresse :</strong> ' . nl2br(htmlspecialchars($donnees_formulaire['origine_adresse'])) . '</p>
        <p><strong>Code Postal :</strong> ' . htmlspecialchars($donnees_formulaire['origine_cp']) . '</p>
        <p><strong>Ville :</strong> ' . htmlspecialchars($donnees_formulaire['origine_ville']) . '</p>
        <p><strong>Pays :</strong> ' . htmlspecialchars($donnees_formulaire['origine_pays']) . '</p>
        <p><strong>Instructions :</strong> ' . nl2br(htmlspecialchars($donnees_formulaire['origine_instructions'])) . '</p>
    </fieldset>

    <fieldset>
        <legend>Lieu de Livraison (Destination)</legend>
        <p><strong>Adresse :</strong> ' . nl2br(htmlspecialchars($donnees_formulaire['destination_adresse'])) . '</p>
        <p><strong>Code Postal :</strong> ' . htmlspecialchars($donnees_formulaire['destination_cp']) . '</p>
        <p><strong>Ville :</strong> ' . htmlspecialchars($donnees_formulaire['destination_ville']) . '</p>
        <p><strong>Pays :</strong> ' . htmlspecialchars($donnees_formulaire['destination_pays']) . '</p>
        <p><strong>Instructions :</strong> ' . nl2br(htmlspecialchars($donnees_formulaire['destination_instructions'])) . '</p>
    </fieldset>

    <fieldset>
        <legend>Description de la Marchandise</legend>
        <p><strong>Nature de la marchandise :</strong> ' . htmlspecialchars($donnees_formulaire['nature_marchandise']) . '</p>
        <p><strong>Code douanier (HS) :</strong> ' . htmlspecialchars($donnees_formulaire['code_douanier_hs']) . '</p>

        <p class="section-title">Colisage :</p>
        <table>
            <thead>
                <tr>
                    <th>Type d\'emballage</th>
                    <th>Nombre</th>
                    <th>Dimensions (L x l x H cm)</th>
                    <th>Poids unitaire (kg)</th>
                    <th>Gerbable</th>
                </tr>
            </thead>
            <tbody>';

// Boucle sur les colis (ici un seul type pour l'instant)
foreach ($donnees_formulaire['colis'] as $colis) {
    $type_emballage_display = $colis['type_emballage'];
    if ($colis['type_emballage'] === 'autre_emballage' && !empty($colis['autre_emballage_details'])) {
        $type_emballage_display .= ' (' . htmlspecialchars($colis['autre_emballage_details']) . ')';
    }

    $dimensions_display = '';
    if ($colis['longueur'] !== null || $colis['largeur'] !== null || $colis['hauteur'] !== null) {
        $dimensions_display = htmlspecialchars($colis['longueur']) . ' x ' . htmlspecialchars($colis['largeur']) . ' x ' . htmlspecialchars($colis['hauteur']) . ' cm';
    }

     $gerbable_display = $colis['gerbable'];
     if ($colis['gerbable'] === 'partiellement' && !empty($colis['gerbable_details'])) {
         $gerbable_display .= ' (' . htmlspecialchars($colis['gerbable_details']) . ')';
     }


    $html_content .= '
                <tr>
                    <td>' . $type_emballage_display . '</td>
                    <td>' . htmlspecialchars($colis['nombre']) . '</td>
                    <td>' . $dimensions_display . '</td>
                    <td>' . htmlspecialchars($colis['poids_unitaire']) . ' kg</td>
                    <td>' . $gerbable_display . '</td>
                </tr>';
}

$html_content .= '
            </tbody>
        </table>

        <p><strong>Poids total brut :</strong> ' . htmlspecialchars($donnees_formulaire['poids_total']) . ' kg</p>
        <p><strong>Volume total :</strong> ' . htmlspecialchars($donnees_formulaire['volume_total']) . ' m³</p>
        <p><strong>Valeur de la marchandise :</strong> ' . htmlspecialchars($donnees_formulaire['valeur_marchandise']) . ' ' . htmlspecialchars($donnees_formulaire['devise_valeur']) . '</p>
        <p><strong>Assurance Ad Valorem souhaitée :</strong> ' . htmlspecialchars($donnees_formulaire['assurance_ad_valorem']) . '</p>

        <p class="section-title">Nature Spécifique de la Marchandise :</p>
        <ul>';
        if (empty($donnees_formulaire['specificites'])) {
            $html_content .= '<li>Aucune spécificité déclarée.</li>';
        } else {
            foreach ($donnees_formulaire['specificites'] as $spec) {
                $html_content .= '<li>' . htmlspecialchars($spec);
                if ($spec === 'dangereuse' && (!empty($donnees_formulaire['classe_onu']) || !empty($donnees_formulaire['fiche_securite']))) {
                     $html_content .= ' (Classe/ONU : ' . htmlspecialchars($donnees_formulaire['classe_onu']) . ', FDS disponible : ' . htmlspecialchars($donnees_formulaire['fiche_securite']) . ')';
                }
                 if ($spec === 'temperature_dirigee' && !empty($donnees_formulaire['temperature_requise'])) {
                     $html_content .= ' (Température requise : ' . htmlspecialchars($donnees_formulaire['temperature_requise']) . '°C)';
                 }
                $html_content .= '</li>';
            }
        }
$html_content .= '
        </ul>
    </fieldset>

    <fieldset>
        <legend>Dates et Documents</legend>
        <p><strong>Date d\'enlèvement souhaitée :</strong> ' . htmlspecialchars($donnees_formulaire['date_enlevement_souhaitee']) . '</p>
        <p><strong>Date de livraison impérative :</strong> ' . (empty($donnees_formulaire['date_livraison_imperative']) ? 'Non spécifiée' : htmlspecialchars($donnees_formulaire['date_livraison_imperative'])) . '</p>
        <p class="section-title">Documents disponibles :</p>
        <ul>';
        if (empty($donnees_formulaire['documents'])) {
            $html_content .= '<li>Aucun document spécifié.</li>';
        } else {
            foreach ($donnees_formulaire['documents'] as $doc) {
                $html_content .= '<li>' . htmlspecialchars($doc) . '</li>';
            }
        }
$html_content .= '
        </ul>
    </fieldset>

    <fieldset>
        <legend>Informations Complémentaires</legend>
        <p><strong>Budget approximatif :</strong> ' . htmlspecialchars($donnees_formulaire['budget_approx']) . '</p>
        <p><strong>Commentaires :</strong> ' . nl2br(htmlspecialchars($donnees_formulaire['commentaires'])) . '</p>
         <p><strong>Contrat de transport accepté :</strong> ' . $donnees_formulaire['contrat_accepte'] . '</p>
    </fieldset>

</body>
</html>';


// --- PARTIE 1 : GÉNÉRATION DU PDF ---
// Cette partie nécessite une bibliothèque PHP comme Dompdf.
// Vous devrez installer Dompdf sur votre serveur (via Composer de préférence).
// Documentation Dompdf : https://github.com/dompdf/dompdf

require_once 'vendor/autoload.php'; // Chemin vers l'autoload de Composer si vous utilisez Composer

use Dompdf\Dompdf;
use Dompdf\Options;

// Configuration de Dompdf (facultatif mais recommandé)
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true); // Permet de charger des images externes si nécessaire (attention sécurité)
$dompdf = new Dompdf($options);

// Charger le HTML dans Dompdf
$dompdf->loadHtml($html_content);

// (Optionnel) Définir la taille et l'orientation du papier
$dompdf->setPaper('A4', 'portrait');

// Rendre le HTML en PDF
$dompdf->render();

// Obtenir le contenu du PDF généré
$pdf_content = $dompdf->output();

// Définir un nom de fichier pour le PDF
$pdf_filename = 'demande_devis_' . date('YmdHis') . '.pdf';
$pdf_filepath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $pdf_filename; // Chemin temporaire pour sauvegarder le PDF

// Sauvegarder le PDF dans un fichier temporaire (nécessaire pour l'attacher à l'email)
file_put_contents($pdf_filepath, $pdf_content);


// --- PARTIE 2 : ENVOI DE L'EMAIL ---
// Cette partie nécessite une bibliothèque PHP pour l'envoi d'emails (PHPMailer recommandé)
// ou l'utilisation des fonctions mail() de PHP (moins fiable).
// Nous allons utiliser PHPMailer dans cet exemple conceptuel.
// Documentation PHPMailer : https://github.com/PHPMailer/PHPMailer

require_once 'vendor/autoload.php'; // Assurez-vous que PHPMailer est chargé via Composer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true); // Passer `true` active les exceptions

try {
    // Paramètres du serveur (SMTP est recommandé pour une meilleure délivrabilité)
    // Configurez ces paramètres avec ceux de votre serveur d'envoi d'emails
    // $mail->SMTPDebug = 2; // Activer la sortie de debug (pour le test)
    $mail->isSMTP(); // Envoyer via SMTP
    $mail->Host       = 'votre_serveur_smtp'; // REMPLACEZ CECI
    $mail->SMTPAuth   = true; // Activer l'authentification SMTP
    $mail->Username   = 'votre_email_smtp'; // REMPLACEZ CECI (souvent la même que $destinataire_email ou une adresse dédiée)
    $mail->Password   = 'votre_mot_de_passe_smtp'; // REMPLACEZ CECI
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Activer le chiffrement TLS implicite (ou STARTTLS)
    $mail->Port       = 465; // Port pour SMTPS (ou 587 pour STARTTLS)

    // Destinataires
    $mail->setFrom('noreply@votredomaine.com', 'Formulaire Devis'); // L'adresse d'envoi (doit souvent exister sur votre domaine)
    $mail->addAddress($destinataire_email); // Votre adresse email pour recevoir le devis
    $mail->addReplyTo($email, $nom_contact); // Pour pouvoir répondre directement au client

    // Pièce jointe
    if ($pdf_filepath && file_exists($pdf_filepath)) {
        $mail->addAttachment($pdf_filepath, $pdf_filename); // Ajouter le PDF généré
    }

     // Ajouter le fichier joint original si présent
    if ($fichier_joint_path && file_exists($fichier_joint_path)) {
         $mail->addAttachment($fichier_joint_path, $_FILES['fichier_joint']['name']);
    }


    // Contenu de l'email
    $mail->isHTML(true); // Définir le format de l'email en HTML
    $mail->Subject = $sujet_email . ' de ' . $nom_contact;
    $mail->Body    = '
        <h1>Nouvelle demande de devis reçue</h1>
        <p>Vous avez reçu une nouvelle demande de devis via le formulaire de votre site.</p>
        <p>Les détails complets sont joints en pièce jointe (PDF).</p>
        <h2>Résumé des informations de contact :</h2>
        <p><strong>Nom :</strong> ' . htmlspecialchars($nom_contact) . '</p>
        <p><strong>Société :</strong> ' . htmlspecialchars($societe) . '</p>
        <p><strong>Email :</strong> ' . htmlspecialchars($email) . '</p>
        <p><strong>Téléphone :</strong> ' . htmlspecialchars($telephone) . '</p>
        <p><strong>Référence interne client :</strong> ' . htmlspecialchars($reference_interne) . '</p>
        <p><strong>Contrat accepté :</strong> ' . $donnees_formulaire['contrat_accepte'] . '</p>
        <br>
        <p>Veuillez consulter le fichier PDF joint pour tous les détails de la demande.</p>
    ';
    $mail->AltBody = 'Nouvelle demande de devis reçue de ' . $nom_contact . '. Les détails sont dans le PDF joint.'; // Texte brut pour les clients mail qui ne supportent pas le HTML

    $mail->send();

    // --- Nettoyage ---
    // Supprimer les fichiers temporaires après l'envoi
    if ($pdf_filepath && file_exists($pdf_filepath)) {
        unlink($pdf_filepath);
    }
    if ($fichier_joint_path && file_exists($fichier_joint_path)) {
         unlink($fichier_joint_path);
    }


    // --- Redirection vers la page de succès ---
    header("Location: " . $url_page_succes);
    exit;

} catch (Exception $e) {
    // --- Gestion des Erreurs d'Envoi ---
    // En cas d'erreur d'envoi, rediriger vers la page d'erreur
    // Vous pouvez logger l'erreur sur le serveur pour investigation : error_log("Mail could not be sent. Mailer Error: {$mail->ErrorInfo}");

    // Nettoyer les fichiers temporaires même en cas d'erreur d'envoi
    if ($pdf_filepath && file_exists($pdf_filepath)) {
        unlink($pdf_filepath);
    }
     if ($fichier_joint_path && file_exists($fichier_joint_path)) {
         unlink($fichier_joint_path);
    }

    header("Location: " . $url_page_erreur . "?error=mail_failed");
    exit;
}

?>
