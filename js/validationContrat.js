function validerForm() {
  const form = document.forms["formContrat"];
  let isValid = true;

  // Réinitialiser les styles
  for (const element of form.elements) {
    element.style.border = "";
  }
  // User Id
  if (form["idutilisateur"].value.trim() === "") {
    form["idutilisateur"].style.border = "2px solid red";
    alert("Veuillez entrer un utilisateur id.");
    isValid = false;
    return isValid;
  }
  // Startup Id
  if (form["idstartup"].value.trim() === "") {
    form["idstartup"].style.border = "2px solid red";
    alert("Veuillez entrer un startup id.");
    isValid = false;
    return isValid;
  }

  // Type de contrat
  if (form["typecontrat"].value.trim() === "") {
    form["typecontrat"].style.border = "2px solid red";
    alert("Veuillez entrer un type de contrat.");
    isValid = false;
    return isValid;
  }

  // Date contrat
  if (form["datecontrat"].value === "") {
    form["datecontrat"].style.border = "2px solid red";
    alert("Veuillez entrer une date de contrat.");
    isValid = false;
    return isValid;
  }

  // Durée
  const duree = parseInt(form["dureecontrat"].value);
  if (isNaN(duree) || duree <= 0) {
    form["dureecontrat"].style.border = "2px solid red";
    alert("Veuillez entrer une durée valide en mois.");
    isValid = false;
    return isValid;
  }

  // Clause sortie
  if (form["clauseSortie"].value.trim() === "") {
    form["clauseSortie"].style.border = "2px solid red";
    alert("Veuillez taper un clause de sortie.");
    isValid = false;
    return isValid;
  }

  // Pourcentage
  const pourcentage = parseFloat(form["pourcentageCaptiale"].value);
  if (isNaN(pourcentage) || pourcentage <= 0 || pourcentage > 100) {
    form["pourcentageCaptiale"].style.border = "2px solid red";
    alert("Veuillez entrer un pourcentage valide (entre 0 et 100).");
    isValid = false;
    return isValid;
  }

  // Valeur startup
  const valeur = parseFloat(form["valeurStartup"].value);
  if (isNaN(valeur) || valeur <= 0) {
    form["valeurStartup"].style.border = "2px solid red";
    alert("Veuillez entrer une valeur valide pour la startup.");
    isValid = false;
    return isValid;
  }

  // montant
  const montantStr = form["montant"].value.trim();
  const montant = parseFloat(montantStr);

  if (montantStr === "" || isNaN(montant) || montant <= 0) {
    form["montant"].style.border = "2px solid red";
    alert("Veuillez entrer un montant valide (supérieur à 0).");
    isValid = false;
    return isValid;
  }

  // Retourner false si un champ est invalide, pour empêcher la soumission
}
