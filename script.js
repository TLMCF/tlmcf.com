document.addEventListener("DOMContentLoaded", () => {
  // L'URL unique qui exporte l'intégralité de votre feuille "Taux-reussite" en CSV.
  // C'est la source de données pour le taux de satisfaction.
  const csvUrl = "https://docs.google.com/spreadsheets/d/e/2PACX-1vQfmuZMJFCyNST3Pa69vyDHwt89D_KWolF-AZ62sX7N3Z094tR1fWulavwHD5fmcQ/pub?gid=520631916&single=true&output=csv";

  // Appeler l'API de Google Sheets une seule fois
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

      let foundTauxSatisfaction = "N/A";

      for (let i = 2; i < lines.length; i++) {
        const currentDataLine = lines[i].split(",");
        if (currentDataLine.length > formationIdColIndex && currentDataLine.length > tauxIndex) {
          const currentFormationInCsv = currentDataLine[formationIdColIndex].trim().toUpperCase();
          const currentTaux = currentDataLine[tauxIndex].trim();

          // Taux de satisfaction (recherche de la ligne "SATISFACTION")
          if (currentFormationInCsv === "SATISFACTION") {
            foundTauxSatisfaction = currentTaux;
            break; // Arrête la boucle une fois que la ligne est trouvée
          }
        }
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
      console.error("Erreur lors du chargement du taux de satisfaction :", error);
      const satisfactionEl = document.getElementById('satisfaction-pourcentage');
      if (satisfactionEl) {
        satisfactionEl.textContent = "Erreur de chargement";
      }
    });
});