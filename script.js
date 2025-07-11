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
      const tauxIndex = headers.indexOf("taux_reussite");

      if (tauxIndex === -1) throw new Error("Colonne 'taux_reussite' non trouvée");

      const dataLine = lines[1].split(",");
      const taux = dataLine[tauxIndex];

      const el = document.querySelector('.pourcentage');
      if (el && taux) {
        el.textContent = `📈 ${taux}% de réussite à l'examen !`;
      }
    })
    .catch(error => {
      console.error("Erreur lors du chargement du taux :", error);
    });
});
