document.addEventListener("DOMContentLoaded", () => {
  // URL pour le taux de réussite
  const csvUrl = "https://docs.google.com/spreadsheets/d/e/2PACX-1vQfmuZMJFCyNST3Pa69vyDHwt89D_KWolF-AZ62sX7N3Z094tR1fWulavwHD5fmcQ/pub?gid=520631916&single=true&output=csv";

  // Bloc de code existant pour le taux de réussite (inchangé)
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

  if (!formationId) {
    const el = document.querySelector('.pourcentage');
    if (el) {
      el.textContent = "Taux non disponible (ID formation manquant)";
    }
  } else {
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
        let foundTaux = "N/A";
        for (let i = 2; i < lines.length; i++) {
          const currentDataLine = lines[i].split(",");
          if (currentDataLine.length > formationIdColIndex && currentDataLine.length > tauxIndex) {
            const currentFormationInCsv = currentDataLine[formationIdColIndex].trim().toUpperCase();
            if (currentFormationInCsv === formationId) {
              foundTaux = currentDataLine[tauxIndex].trim();
              break;
            }
          }
        }
        const el = document.querySelector('.pourcentage');
        if (el) {
          el.textContent = `📈 ${foundTaux} de réussite à l'examen !`;
        }
      })
      .catch(error => {
        console.error("Erreur lors du chargement du taux de réussite :", error);
        const el = document.querySelector('.pourcentage');
        if (el) {
          el.textContent = "Erreur de chargement";
        }
      });
  }
  // === NOUVEAU BLOC POUR LE TAUX DE SATISFACTION (corrigé) ===
// L'ID de votre feuille de satisfaction, extrait de votre lien partagé
const satisfactionSheetId = "1f5d7jF1Mr6bV0D3FGO3PLSB9HsRso8Rl";
// L'identifiant de l'onglet (gid) pour le taux de satisfaction
const satisfactionGid = "1472296115";
// La cellule à récupérer
const satisfactionCell = "K8";

const satisfactionUrl = `https://docs.google.com/spreadsheets/d/${satisfactionSheetId}/gviz/tq?gid=${satisfactionGid}&tqx=out:json&tq=select%20${satisfactionCell.charAt(0)}%20offset%20${parseInt(satisfactionCell.substring(1)) - 1}%20limit%201`;

fetch(satisfactionUrl)
  .then(res => res.text())
  .then(text => {
    const jsonText = text.substring(text.indexOf('{'), text.lastIndexOf('}') + 1);
    const json = JSON.parse(jsonText);
    
    if (json.table && json.table.rows && json.table.rows[0]) {
      const cellValue = json.table.rows[0].c[0].v;
      const element = document.getElementById('satisfaction-pourcentage');
      if (element) {
        element.textContent = `${cellValue}% de satisfaction !`;
      }
    } else {
      document.getElementById('satisfaction-pourcentage').textContent = 'Données non trouvées';
    }
  })
  .catch(error => {
    console.error("Erreur lors du chargement du taux de satisfaction :", error);
    const el = document.getElementById('satisfaction-pourcentage');
    if (el) {
        el.textContent = "Erreur de chargement";
    }
  });

});