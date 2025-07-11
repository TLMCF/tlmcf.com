document.addEventListener("DOMContentLoaded", () => {
  const csvUrl = "https://docs.google.com/spreadsheets/d/e/2PACX-1vQfmuZMJFCyNST3Pa69vyDHwt89D_KWolF-AZ62sX7N3Z094tR1fWulavwHD5fmcQ/pub?gid=520631916&single=true&output=csv";

  // 1. Tenter d'obtenir l'identifiant de la formation depuis le titre H2 de la page
  const pageTitleElement = document.querySelector('section.hero h2');
  let formationId = null;
  if (pageTitleElement) {
    const titleText = pageTitleElement.textContent;
    // Extraire "R-482" de "Formation : R-482"
    const match = titleText.match(/Formation\s*:\s*([A-Z0-9-]+)/i);
    if (match && match[1]) {
      formationId = match[1].trim().toUpperCase();
      console.log("Identifiant de formation extrait de la page :", formationId);
    } else {
      console.warn("Impossible d'extraire l'identifiant de formation du titre H2. Assurez-vous qu'il est au format 'Formation : XXX'.");
    }
  }

  if (!formationId) {
    // Si l'ID n'a pas pu être extrait, ne pas tenter de charger le CSV pour éviter des erreurs
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
      // On cherche aussi l'index de la colonne "Formation" pour pouvoir matcher l'ID
      const formationIdColIndex = cleanedHeaders.findIndex(h => h === "formation");

      if (tauxIndex === -1) {
          throw new Error("Colonne 'taux-reussite' non trouvée dans les en-têtes réelles. Vérifiez les logs ci-dessus.");
      }
      if (formationIdColIndex === -1) {
          throw new Error("Colonne 'Formation' non trouvée dans les en-têtes. Assurez-vous qu'elle existe dans votre Google Sheet.");
      }

      let foundTaux = "N/A"; // Valeur par défaut si non trouvé

      // Parcourir toutes les lignes de données (à partir de l'index 2)
      for (let i = 2; i < lines.length; i++) {
        const currentDataLine = lines[i].split(",");
        // S'assurer que la ligne a assez de colonnes
        if (currentDataLine.length > formationIdColIndex && currentDataLine.length > tauxIndex) {
            const currentFormationInCsv = currentDataLine[formationIdColIndex].trim().toUpperCase();
            // Comparer l'ID de la page avec l'ID trouvé dans le CSV
            if (currentFormationInCsv === formationId) {
                foundTaux = currentDataLine[tauxIndex].trim();
                console.log(`Taux trouvé pour ${formationId} :`, foundTaux);
                break; // Une fois trouvé, on sort de la boucle
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