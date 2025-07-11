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

      // La ligne 1 (index 0) est "Taux de réussites" (titre fusionné)
      // La ligne 2 (index 1) contient maintenant les en-têtes réelles : "Formation", "réussite", "echec", "taux-reussite"
      // La ligne 3 (index 2) contient les données pour "R-482" et le 80%

      const headers = lines[1].split(","); // LIRE LA DEUXIÈME LIGNE (INDEX 1) COMME EN-TÊTES
      console.log("En-têtes brutes lues du CSV (ligne d'index 1) :", headers);

      const cleanedHeaders = headers.map(h => h.trim().toLowerCase().replace(/[^a-z0-9-]/g, ''));
      console.log("En-têtes nettoyées pour comparaison :", cleanedHeaders);

      const tauxIndex = cleanedHeaders.findIndex(h => h === "taux-reussite");

      if (tauxIndex === -1) {
          throw new Error("Colonne 'taux-reussite' non trouvée dans les en-têtes réelles. Vérifiez les logs ci-dessus.");
      }

      const dataLine = lines[2].split(","); // LIRE LA TROISIÈME LIGNE (INDEX 2) COMME DONNÉES
      console.log("Ligne de données brute (ligne d'index 2) :", dataLine);

      const taux = dataLine[tauxIndex] ? dataLine[tauxIndex].trim() : "N/A";
      console.log("Valeur de 'taux' extraite :", taux);

      const el = document.querySelector('.pourcentage');
      if (el) {
        el.textContent = `📈 ${taux} de réussite à l'examen !`;
      }
    })
    .catch(error => {
      console.error("Erreur lors du chargement du taux :", error);
    });
});