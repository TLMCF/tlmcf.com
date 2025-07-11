document.addEventListener("DOMContentLoaded", () => {
  const csvUrl = "https://docs.google.com/spreadsheets/d/e/2PACX-1vQfmuZMJFCyNST3Pa69vyDHwt89D_KWolF-AZ62sX7N3Z094tR1fWulavwHD5fmcQ/pub?gid=520631916&single=true&output=csv";

  // 1. Tenter d'obtenir l'identifiant de la formation depuis le titre H1 ou H2 de la page
  // On va d'abord chercher dans un H1, puis si non trouvé, dans un H2.
  const pageTitleElement = document.querySelector('section.hero h1') || document.querySelector('section.hero h2');
  let formationId = null;

  if (pageTitleElement) {
    const titleText = pageTitleElement.textContent;
    console.log("Texte du titre de la page :", titleText); // Ajout d'un log pour le texte complet du titre

    // Nouvelle logique d'extraction : on cherche explicitement des patterns A-XXX, AC-XXX, R-XXX
    const specificMatch = titleText.match(/([AR][C]?-\d{3,})/i); // Capturera R-482, AC-482, A-123, etc.

    if (specificMatch && specificMatch[1]) {
        formationId = specificMatch[1].trim().toUpperCase();
    } else {
        console.warn("Impossible d'extraire un identifiant de formation de type 'Lettre-Chiffres' (ex: R-482, AC-482) du titre.");
        // Fallback si aucun motif spécifique n'est trouvé, on tente l'ancienne regex pour voir
        const genericMatch = titleText.match(/(?:Formation|Conduite)\s*[:\s]*([A-Z0-9-]+)/i);
        if (genericMatch && genericMatch[1]) {
            formationId = genericMatch[1].trim().toUpperCase();
        } else {
            const simpleEndMatch = titleText.match(/([A-Z0-9-]+)\s*$/i);
            if (simpleEndMatch && simpleEndMatch[1]) {
                formationId = simpleEndMatch[1].trim().toUpperCase();
            }
        }
    }

    if (formationId) {
      console.log("Identifiant de formation extrait de la page :", formationId);
    } else {
      console.error("Aucun identifiant de formation valide n'a pu être extrait du titre.");
    }
  }

  if (!formationId) {
    console.error("Identifiant de formation non trouvé sur la page. Impossible de charger le taux.");
    const el = document.querySelector('.pourcentage');
    if (el) {
      el.textContent = "Taux non disponible (ID formation manquant)";
    }
    return; // Arrêter l'exécution du script
  }


  fetch(csvUrl)
    .then(response => {
      if (!response.ok) {
        throw new Error('Erreur de réseau : ' + response.status);
      }
      return response.text();
    })
    .then(csv => {
      const lines = csv.trim().split("\n");

      // La ligne 1 (index 0) est "Taux de réussites" (titre fusionné)
      // La ligne 2 (index 1) contient les en-têtes réelles
      const headers = lines[1].split(",");
      console.log("En-têtes brutes lues du CSV (ligne d'index 1) :", headers);

      const cleanedHeaders = headers.map(h => h.trim().toLowerCase().replace(/[^a-z0-9-]/g, ''));
      console.log("En-têtes nettoyées pour comparaison :", cleanedHeaders);

      const tauxIndex = cleanedHeaders.findIndex(h => h === "taux-reussite");
      const formationIdColIndex = cleanedHeaders.findIndex(h => h === "formation");

      if (tauxIndex === -1) {
          throw new Error("Colonne 'taux-reussite' non trouvée dans les en-têtes réelles. Vérifiez les logs ci-dessus.");
      }
      if (formationIdColIndex === -1) {
          throw new Error("Colonne 'Formation' non trouvée dans les en-têtes. Assurez-vous qu'elle existe dans votre Google Sheet.");
      }

      let foundTaux = "N/A";

      // Parcourir toutes les lignes de données (à partir de l'index 2)
      for (let i = 2; i < lines.length; i++) {
        const currentDataLine = lines[i].split(",");
        if (currentDataLine.length > formationIdColIndex && currentDataLine.length > tauxIndex) {
            const currentFormationInCsv = currentDataLine[formationIdColIndex].trim().toUpperCase();
            if (currentFormationInCsv === formationId) {
                foundTaux = currentDataLine[tauxIndex].trim();
                console.log(`Taux trouvé pour ${formationId} :`, foundTaux);
                break;
            }
        }
      }

      console.log("Lignes de données lues et recherchées.");
      console.log("Valeur finale de 'taux' :", foundTaux);

      const el = document.querySelector('.pourcentage');
      if (el) {
        el.textContent = `📈 ${foundTaux} de réussite à l'examen !`;
      }
    })
    .catch(error => {
      console.error("Erreur lors du chargement du taux :", error);
    });
});