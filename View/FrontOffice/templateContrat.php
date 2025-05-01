<?php
require_once '../../Model/Contrat.php';

$idcontrat = $_GET['id'] ?? null;
$contrat = Contrat::getContratById($idcontrat);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        h1, h2 { text-align: center; }
        .section { margin: 20px 0; }
    </style>
</head>
<body>
    <h1>Contrat d’Investissement</h1>
    <p><strong>Date :</strong> <?= $contrat['datecontrat'] ?></p>

    <div class="section">
        <h2>Parties Concernées</h2>
        <p><strong>Investisseur :</strong> Utilisateur #<?= $contrat['idutilisateur'] ?></p>
        <p><strong>Startup :</strong> Startup #<?= $contrat['nomStartup'] ?></p>
    </div>

    <div class="section">
        <h2>Détails du Contrat</h2>
        <p><strong>Type :</strong> <?= $contrat['typecontrat'] ?></p>
        <p><strong>Durée :</strong> <?= $contrat['dureecontrat'] ?> mois</p>
        <p><strong>Pourcentage :</strong> <?= $contrat['pourcentageCaptiale'] ?>%</p>
        <p><strong>Valeur Startup :</strong> <?= $contrat['valeurStartup'] ?> €</p>
    </div>

    <div class="section">
        <h2>Clauses</h2>
        <h3>Clauses de sortie</h3>
        <p><?= $contrat['clauseSortie'] ?></p>
        <h3>Condidtion spécifiques</h3>
        <p><?= $contrat['conditionsSpecifique'] ?></p>
    </div>

    <p><strong>Status :</strong> <?= $contrat['statusContrat'] ?></p>

</body>
</html>
