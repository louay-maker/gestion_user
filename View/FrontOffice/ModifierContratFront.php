<?php
require_once '../../Model/Contrat.php';

if (isset($_GET['id'])) {
    $contrat = Contrat::getContratById($_GET['id']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Startup - Startup Website Template</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Free HTML Templates" name="keywords">
    <meta content="Free HTML Templates" name="description">

    <!-- Favicon -->
    <link href="../../img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&family=Rubik:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="../../lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="../../lib/animate/animate.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="../../css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="../../css/style.css" rel="stylesheet">
</head>

<body>


    <!-- Topbar Start -->
    <div class="container-fluid bg-dark px-5 d-none d-lg-block">
        <div class="row gx-0">
            <div class="col-lg-8 text-center text-lg-start mb-2 mb-lg-0">
                <div class="d-inline-flex align-items-center" style="height: 45px;">
                    <small class="me-3 text-light"><i class="fa fa-map-marker-alt me-2"></i>123 Rue Tunis, Tunisie, TN</small>
                    <small class="me-3 text-light"><i class="fa fa-phone-alt me-2"></i>+216 29 999 999</small>
                    <small class="text-light"><i class="fa fa-envelope-open me-2"></i>startupconnect@gmail.com</small>
                </div>
            </div>
            <div class="col-lg-4 text-center text-lg-end">
                <div class="d-inline-flex align-items-center" style="height: 45px;">
                </div>
            </div>
        </div>
    </div>
    <!-- Topbar End -->

   
        <!-- <nav class="navbar navbar-expand-lg navbar-dark px-5 py-3 py-lg-0" style="
    background-color: #06A3DA;"> -->
           
           
    <div class="container-fluid position-relative p-0">
        <nav class="navbar navbar-expand-lg navbar-dark px-5 py-3 py-lg-0"style= "background-color: #06A3DA;">
            <a href="../../index.html" class="navbar-brand p-0">
                <h1 class="m-0"><i class="fa fa-user-tie me-2"></i>StartupUp Connect</h1>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                <span class="fa fa-bars"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <div class="navbar-nav ms-auto py-0">
                    <a href="../../index.html" class="nav-item nav-link">Acceuil</a>
                    <a href="startupList.html" class="nav-item nav-link">Startup</a>
                    <a href="ListContratFront.php" class="nav-item nav-link">Mes contrat</a>
                    <div class="nav-item dropdown" style="color: white;">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Pages</a>
                        <div class="dropdown-menu m-0">
                            <a href="../BackOffice/dashboard.html" class="dropdown-item">Dashboard</a>
                            <a href="#" class="dropdown-item">Gestion utilisateurs</a>
                            <a href="#" class="dropdown-item">Gestion profiles</a>
                            <a href="#" class="dropdown-item">Gestion startup</a>
                            <a href="#" class="dropdown-item">Gestion evénements</a>
                            <a href="../BackOffice/gestionInvestissement.html" class="dropdown-item">Gestion des investissements</a>
                            <a href="#" class="dropdown-item">Gestion documents</a>
                        </div>
                    </div>
                    <a href="../FrontOffice/login.html" class="nav-item nav-link">Connexion</a>
                </div>
            </div>
        </nav>
    </div>
        





<div class="container py-5" style="margin-top: 100px !important; ">
  <div class="col-md-6"> <h1>Ajouter contrat</h1></div>
  <div style="margin-bottom: 20px;" >
    <a href="ListContratFront.php" >← Retour</a>
</div>
    <div class="card shadow-lg p-3 mb-5 bg-white rounded">
        <div class="card-body">
            <form name="formContrat" method="post" onsubmit="return validerForm()"  action="../../Controller/ContratController.php">
                <div class="form-row"  >
                <input type="hidden" name="idcontrat" value="<?= $contrat['idcontrat'] ?>">
                 <input type="hidden" name="idutilisateur" value="<?= $contrat['idutilisateur'] ?>">
                 <input type="hidden" name="idstartup" value="<?= $contrat['idstartup'] ?>">
                 <input type="hidden" name="statusContrat" value="<?= $contrat['statusContrat'] ?>">
                  <div class="form-group col-md-6"  style="margin-bottom: 10px;">
                    <label for="inputEmail4">Type contrat</label>
                    <input type="text" class="form-control" name="typecontrat" value="<?= $contrat['typecontrat'] ?>" >
                  </div>
                  <div class="form-group col-md-6"  style="margin-bottom: 10px;">
                    <label for="inputPassword4">Date contrat</label>
                    <input type="date" class="form-control" name="datecontrat" value="<?= $contrat['datecontrat'] ?>">
                  </div>
                </div>
                <div class="form-group col-md-3"  style="margin-bottom: 10px;">
                  <label for="inputAddress">Duree contrat</label>
                  <input type="text" class="form-control" name="dureecontrat" value="<?= $contrat['dureecontrat'] ?>" placeholder="Duree contrat en mois">
                </div>
                <div class="form-group"  style="margin-bottom: 10px;">
                  <label for="inputAddress2">Clause sortie </label>
                  <input type="text" class="form-control" name="clauseSortie" value="<?= $contrat['clauseSortie'] ?>">
                </div>
                <div class="form-row">
                  <div class="form-group col-md-3"  style="margin-bottom: 10px;">
                    <label for="inputCity">Pourcentage capitale</label>
                    <input type="text" class="form-control" name="pourcentageCaptiale" value="<?= $contrat['pourcentageCaptiale'] ?>">
                  </div>
                  <div class="form-group col-md-3"  style="margin-bottom: 10px;">
                    <label for="inputState">valeur Startup</label>
                    <input type="text" class="form-control" name="valeurStartup" value="<?= $contrat['valeurStartup'] ?>">
                  </div>
                  <div class="form-group"  style="margin-bottom: 10px;">
                    <label for="inputZip">conditions Specifiques</label>
                    <input type="text" class="form-control" name="conditionsSpecifique" value="<?= $contrat['conditionsSpecifique'] ?>">
                  </div>
                  <div class="form-group"  style="margin-bottom: 10px;">
                    <label for="inputZip">montant</label>
                    <input type="text" class="form-control" name="montant" value="<?= $contrat['montant'] ?>">
                  </div>
                </div>
                <div style="margin-top: 10px;">
                  <button type="submit" class="btn btn-primary" name="modifier" value="Modifier">Modifier  </button>
                  <button class="btn btn-danger" type="submit"  name="annuler" value="Annuler">Annuler  </button></a>  
                </div>
                
              </form>
             
        </div>
   
    </div>
</div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../lib/wow/wow.min.js"></script>
    <script src="../../lib/easing/easing.min.js"></script>
    <script src="../../lib/waypoints/waypoints.min.js"></script>
    <script src="../../lib/counterup/counterup.min.js"></script>
    <script src="../../lib/owlcarousel/owl.carousel.min.js"></script>
    <script  src="../../js/validationContrat.js" ></script>
    <!-- Template Javascript -->
    <script src="../../js/main.js"></script>


</body>

