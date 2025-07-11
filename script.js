document.addEventListener("DOMContentLoaded", () => {
  const csvUrl = "https://docs.google.com/spreadsheets/d/e/2PACX-1vQfmuZMJFCyNST3Pa69vyDHwt89D_KWolF-AZ62sX7N3Z094tR1fWulavwHD5fmcQ/pub?gid=520631916&single=true&output=csv";

  fetch(csvUrl)
    .then(response => {
      if (!response.ok) {
        throw new Error('Erreur de réseau : ' + response.status);
      }
      return response.text();
    })
    .then(csv => {
      const lines = csv.trim().split("\n");
      const headers = lines[0].split(",");
      // Nettoyer les en-têtes pour enlever tout sauf les lettres, chiffres et tirets.
      // Cela aide à ignorer les caractères invisibles ou les problèmes d'encodage.
      const cleanedHeaders = headers.map(h => h.trim().toLowerCase().replace(/[^a-z0-9-]/g, ''));

      const tauxIndex = cleanedHeaders.findIndex(h => h === "taux-reussite");

      if (tauxIndex === -1) {
          // Afficher les en-têtes brutes et nettoyées pour faciliter le débogage si l'erreur persiste
          console.error("En-têtes brutes du CSV :", headers);
          console.error("En-têtes nettoyées :", cleanedHeaders);
          throw new Error("Colonne 'taux-reussite' non trouvée après nettoyage. Vérifiez les en-têtes.");
      }

      const dataLine = lines[1].split(",");
      const taux = dataLine[tauxIndex].trim();

      const el = document.querySelector('.pourcentage');
      if (el && taux) {
        el.textContent = `📈 ${taux} de réussite à l'examen !`;
      }
    })
    .catch(error => {
      console.error("Erreur lors du chargement du taux :", error);
    });
});