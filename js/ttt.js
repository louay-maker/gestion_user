function validerForm() {
    const form = document.forms["formContrat"];
    let isValid = true;

    // Réinitialiser les styles et les messages d'erreur précédents
    for (const element of form.elements) {
        element.style.border = "";
        const errorMessage = document.getElementById(element.name + "-error");
        if (errorMessage) {
            errorMessage.remove(); // Supprime le message d'erreur existant si il y en a
        }
    }

    // Type de contrat
    if (form["typecontrat"].value.trim() === "") {
        form["typecontrat"].style.border = "2px solid red";
        const errorMessage = document.createElement("span");
        errorMessage.id = "typecontrat-error";
        errorMessage.style.color = "red";
        errorMessage.innerText = "Le type de contrat est requis.";
        form["typecontrat"].parentNode.appendChild(errorMessage); // Ajoute le message d'erreur sous le champ
        isValid = false;
    }

    // Date contrat
    if (form["datecontrat"].value === "") {
        form["datecontrat"].style.border = "2px solid red";
        const errorMessage = document.createElement("span");
        errorMessage.id = "datecontrat-error";
        errorMessage.style.color = "red";
        errorMessage.innerText = "Veuillez entrer une date de contrat.";
        form["datecontrat"].parentNode.appendChild(errorMessage); 
        isValid = false;
    }

    // Durée
    const duree = parseInt(form["dureecontrat"].value);
    if (isNaN(duree) || duree <= 0) {
        form["dureecontrat"].style.border = "2px solid red";
        const errorMessage = document.createElement("span");
        errorMessage.id = "dureecontrat-error";
        errorMessage.style.color = "red";
        errorMessage.innerText = "La durée du contrat doit être un nombre positif.";
        form["dureecontrat"].parentNode.appendChild(errorMessage); 
        isValid = false;
    }

    // Pourcentage
    const pourcentage = parseFloat(form["pourcentageCaptiale"].value);
    if (isNaN(pourcentage) || pourcentage <= 0 || pourcentage > 100) {
        form["pourcentageCaptiale"].style.border = "2px solid red";
        const errorMessage = document.createElement("span");
        errorMessage.id = "pourcentageCaptiale-error";
        errorMessage.style.color = "red";
        errorMessage.innerText = "Le pourcentage doit être un nombre entre 0 et 100.";
        form["pourcentageCaptiale"].parentNode.appendChild(errorMessage); 
        isValid = false;
    }

    // Valeur startup
    const valeur = parseFloat(form["valeurStartup"].value);
    if (isNaN(valeur) || valeur <= 0) {
        form["valeurStartup"].style.border = "2px solid red";
        const errorMessage = document.createElement("span");
        errorMessage.id = "valeurStartup-error";
        errorMessage.style.color = "red";
        errorMessage.innerText = "La valeur de la startup doit être un nombre positif.";
        form["valeurStartup"].parentNode.appendChild(errorMessage); 
        isValid = false;
    }

    // Si tout est valide, on renvoie true, sinon false
    return isValid;
}
