<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Utilisateurs - Startup Connect</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .table-actions {
            white-space: nowrap;
        }
        .form-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container-fluid mt-4">
        <h2 class="mb-4">Gestion des Utilisateurs</h2>
        
        <!-- Formulaire d'ajout/modification -->
        <div class="form-section">
            <h4>Ajouter/Modifier un Utilisateur</h4>
            <form id="userForm" method="POST" action="../Controller/usercontroller.php">
                <input type="hidden" id="userId" name="userId">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="nom" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="nom" name="nom" required>
                    </div>
                    <div class="col-md-4">
                        <label for="prenom" class="form-label">Prénom</label>
                        <input type="text" class="form-control" id="prenom" name="prenom" required>
                    </div>
                    <div class="col-md-4">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="col-md-4">
                        <label for="motdepasse" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" id="motdepasse" name="motdepasse" required>
                        <small class="text-muted">Minimum 8 caractères, une majuscule et un chiffre</small>
                    </div>
                    <div class="col-md-4">
                        <label for="nom_role" class="form-label">Rôle</label>
                        <select class="form-select" id="nom_role" name="nom_role" required>
                            <option value="">Sélectionner...</option>
                            <option value="admin">Administrateur</option>
                            <option value="user">Utilisateur</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="role_id" class="form-label">ID Rôle</label>
                        <select class="form-select" id="role_id" name="role_id" required>
                            <option value="1">1 - Administrateur</option>
                            <option value="2">2 - Utilisateur</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" name="signup" class="btn btn-primary">Enregistrer</button>
                        <button type="button" class="btn btn-secondary" id="resetBtn">Annuler</button>
                    </div>
                </div>
            </form>
            <?php if(isset($_SESSION['error'])): ?>
                <div class="alert alert-danger mt-3">
                    <?php 
                        echo $_SESSION['error'];
                        unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>
            <?php if(isset($_SESSION['success'])): ?>
                <div class="alert alert-success mt-3">
                    <?php 
                        echo $_SESSION['success'];
                        unset($_SESSION['success']);
                    ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Tableau des utilisateurs -->
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    require_once '../Model/user.php';
                    $utilisateurs = Utilisateur::getAllUtilisateurs();
                    foreach($utilisateurs as $user): 
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                        <td><?php echo htmlspecialchars($user['nom']); ?></td>
                        <td><?php echo htmlspecialchars($user['prenom']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['nom_role']); ?></td>
                        <td class="table-actions">
                            <button class="btn btn-sm btn-warning edit-btn" data-id="<?php echo $user['id']; ?>">
                                <i class="fas fa-edit"></i> Modifier
                            </button>
                            <form action="../Controller/usercontroller.php" method="GET" style="display: inline;">
                                <input type="hidden" name="supprimer" value="<?php echo $user['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                                    <i class="fas fa-trash"></i> Supprimer
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Réinitialisation du formulaire
            document.getElementById('resetBtn').addEventListener('click', function() {
                document.getElementById('userForm').reset();
                document.getElementById('userId').value = '';
            });

            // Validation du formulaire
            document.getElementById('userForm').addEventListener('submit', function(e) {
                const password = document.getElementById('motdepasse').value;
                if (password.length < 8 || !/[A-Z]/.test(password) || !/[0-9]/.test(password)) {
                    e.preventDefault();
                    alert('Le mot de passe doit contenir au moins 8 caractères, une majuscule et un chiffre');
                }
            });

            // Gestion de l'édition
            document.querySelectorAll('.edit-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const userId = this.getAttribute('data-id');
                    // Ici vous pouvez ajouter la logique pour charger les données de l'utilisateur
                    // via une requête AJAX vers votre contrôleur
                });
            });
        });
    </script>
</body>
</html>
