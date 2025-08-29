document.addEventListener("DOMContentLoaded", () => {
  // URL pour le taux de réussite (ne change pas)
  const csvUrlTauxReussite = "https://docs.google.com/spreadsheets/d/e/2PACX-1vQfmuZMJFCyNST3Pa69vyDHwt89D_KWolF-AZ62sX7N3Z094tR1fWulavwHD5fmcQ/pub?gid=520631916&single=true&output=csv";

  // ⚠️ Remplacez VOTRE_URL_DU_TAUX_DE_SATISFACTION par l'URL que vous avez copiée
  const csvUrlTauxSatisfaction = "VOTRE_URL_DU_TAUX_DE_SATISFACTION";

  // ... (Le reste du code pour le taux de réussite) ...
  // Pas besoin de le recopier ici, c'est la partie de votre script existant qui va de `const pageTitleElement = ...` jusqu'à `el.textContent = ...`
  // Collez ce qui suit juste après ce premier fetch.


  // === NOUVEAU BLOC POUR LE TAUX DE SATISFACTION ===
  
  // Cette partie du script charge le taux de satisfaction, sans se soucier de l'ID de la formation.
  fetch(csvUrlTauxSatisfaction)
    .then(response => {
      if (!response.ok) {
        throw new Error('Erreur de réseau lors du chargement de la satisfaction : ' + response.status);
      }
      return response.text();
    })
    .then(csv => {
      // Les données sont dans une seule ligne, car le taux de satisfaction est une valeur unique
      const lines = csv.trim().split("\n");
      
      // Ici, on sait que le taux est dans la 15ème ligne et 6ème colonne (F) du CSV si votre fichier est bien organisé
      // Pour cibler la cellule F15, le CSV est structuré différemment d'un tableur.
      // Il faut trouver la bonne ligne et la bonne colonne.
      // Si "Taux de satisfaction" est un titre, on peut chercher la ligne qui le contient.
      const satisfactionLine = lines.find(line => line.includes('Taux de satisfaction'));
      
      let foundTauxSatisfaction = "N/A";
      if (satisfactionLine) {
        const data = satisfactionLine.split(",");
        // La valeur "100%" semble être dans la 6ème colonne (index 5)
        foundTauxSatisfaction = data[5].trim(); 
        console.log("Taux de satisfaction trouvé :", foundTauxSatisfaction);
      } else {
          console.warn("Taux de satisfaction non trouvé dans la feuille CSV.");
      }

      // ⚠️ IMPORTANT : Mettez à jour l'élément HTML correct.
      // Assurez-vous d'avoir un élément <p id="satisfaction-pourcentage"> dans votre HTML
      const el = document.getElementById('satisfaction-pourcentage');
      if (el) {
          el.textContent = `👌🏻 ${foundTauxSatisfaction} de satisfaction !`;
      }
    })
    .catch(error => {
      console.error("Erreur lors du chargement du taux de satisfaction :", error);
    });
});