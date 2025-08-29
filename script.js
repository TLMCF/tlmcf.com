document.addEventListener("DOMContentLoaded", () => {
  // URL pour le taux de réussite
  const csvUrl = "https://docs.google.com/spreadsheets/d/e/2PACX-1vQfmuZMJFCyNST3Pa69vyDHwt89D_KWolF-AZ62sX7N3Z094tR1fWulavwHD5fmcQ/pub?gid=520631916&single=true&output=csv";

  // Bloc de code pour les deux taux
  const pageTitleElement = document.querySelector('section.hero h1') || document.querySelector('section.hero h2');
  let formationId = null;

  if (pageTitleElement) {
    const titleText = pageTitleElement.textContent;
    const specificMatch = titleText.match(/([AR][C]?-\d{3,})/i);
    if (specificMatch && specificMatch[1]) {
      formationId = specificMatch[1].trim().toUpperCase();
    } else {
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
  }

  fetch(csvUrl)
    .then(response => {
      if (!response.ok) throw new Error('Erreur de réseau : ' + response.status);
      return response.text();
    })
    .then(csv => {
      const lines = csv.trim().split("\n");
      const headers = lines[1].split(",");
      const cleanedHeaders = headers.map(h => h.trim().toLowerCase().replace(/[^a-z0-9-]/g, ''));
      const tauxIndex = cleanedHeaders.findIndex(h => h === "taux-reussite");
      const formationIdColIndex = cleanedHeaders.findIndex(h => h === "formation");
      if (tauxIndex === -1 || formationIdColIndex === -1) {
        throw new Error("Colonnes 'taux-reussite' ou 'Formation' non trouvées.");
      }

      let foundTauxReussite = "N/A";
      let foundTauxSatisfaction = "N/A";

      for (let i = 2; i < lines.length; i++) {
        const currentDataLine = lines[i].split(",");
        if (currentDataLine.length > formationIdColIndex && currentDataLine.length > tauxIndex) {
          const currentFormationInCsv = currentDataLine[formationIdColIndex].trim().toUpperCase();
          const currentTaux = currentDataLine[tauxIndex].trim();

          // Taux de réussite pour la formation en cours
          if (formationId && currentFormationInCsv === formationId) {
            foundTauxReussite = currentTaux;
          }

          // Taux de satisfaction (recherche de la ligne "SATISFACTION")
          if (currentFormationInCsv === "SATISFACTION") {
            foundTauxSatisfaction = currentTaux;
          }
        }
      }

      // Afficher le taux de réussite
      const reussiteEl = document.querySelector('.pourcentage');
      if (reussiteEl && formationId) {
        reussiteEl.textContent = `📈 ${foundTauxReussite} de réussite à l'examen !`;
      } else if (reussiteEl) {
        reussiteEl.textContent = "Taux non disponible (ID formation manquant)";
      }

      // Afficher le taux de satisfaction
      const satisfactionEl = document.getElementById('satisfaction-pourcentage');
      if (satisfactionEl) {
        if (foundTauxSatisfaction !== "N/A") {
          satisfactionEl.textContent = `${foundTauxSatisfaction}% de satisfaction !`;
        } else {
          satisfactionEl.textContent = "Données non trouvées";
        }
      }
    })
    .catch(error => {
      console.error("Erreur lors du chargement des taux :", error);
      const reussiteEl = document.querySelector('.pourcentage');
      if (reussiteEl) {
        reussiteEl.textContent = "Erreur de chargement";
      }
      const satisfactionEl = document.getElementById('satisfaction-pourcentage');
      if (satisfactionEl) {
        satisfactionEl.textContent = "Erreur de chargement";
      }
    });
});