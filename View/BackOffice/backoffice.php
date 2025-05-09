<?php
// backoffice.php (dans View/backoffice)

// 1) Includes et démarrage de session
require_once dirname(__DIR__, 2) . '/config.php';
require_once dirname(__DIR__, 2) . '/controller/FunctionsController.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2) Instanciation du controller
$controller = new FunctionsController($conn);

// 3) Traitement du POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'create') {
            $data = [
                'Nom'        => $_POST['Nom'],
                'Prenom'     => $_POST['Prenom'],
                'Email'      => $_POST['Email'],
                'MotDePasse' => $_POST['MotDePasse'],
                'Role_ID'    => $_POST['Role_ID'] ?? 2,
                'avatar'     => $_POST['avatar'] ?? null,
            ];
            $newId = $controller->createUser($data);
            if ($newId) {
                $_SESSION['flash_success'] = "Utilisateur créé avec succès (ID = $newId).";
            } else {
                $_SESSION['flash_error'] = "Erreur lors de la création de l’utilisateur.";
            }
        }
        elseif ($action === 'update') {
            $data = [
                'Nom'     => $_POST[ 'Nom'],
                'Prenom'  => $_POST['Prenom'],
                'Email'   => $_POST['Email'],
                'Role_ID' => $_POST['Role_ID'],
            ];
            if (!empty($_POST['MotDePasse'])) {
                $data['MotDePasse'] = $_POST['MotDePasse'];
            }
            $ok = $controller->updateUser((int)$_POST['id'], $data);
            $_SESSION['flash_success'] = $ok
                ? "Utilisateur #".$_POST['id']." mis à jour."
                : "Aucune modification effectuée.";
        }
        elseif ($action === 'delete') {
            $ok = $controller->deleteUser((int)$_POST['id']);
            $_SESSION['flash_success'] = $ok
                ? "Utilisateur #".$_POST['id']." supprimé."
                : "Impossible de supprimer l’utilisateur #".$_POST['id'].".";
        }
    } catch (Exception $e) {
        $_SESSION['flash_error'] = "Erreur : " . $e->getMessage();
    }

    // 4) Redirection pour effacer le POST
    header('Location: backoffice.php');
    exit;
}

// 5) Lecture des utilisateurs
$users = $controller->getAllUsers();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Backoffice Utilisateurs</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h1 class="mb-4">Gestion des Utilisateurs</h1>

    <!-- Flash messages -->
    <?php if (!empty($_SESSION['flash_success'])): ?>
      <div class="alert alert-success">
        <?= htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?>
      </div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash_error'])): ?>
      <div class="alert alert-danger">
        <?= htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?>
      </div>
    <?php endif; ?>

    <!-- Formulaire d’ajout -->
    <div class="card mb-4">
      <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
          <input type="hidden" name="action" value="create">
          <div class="row g-3">
            <div class="col-md-3"><input type="text"     name="Nom"        class="form-control" placeholder="Nom"        required></div>
            <div class="col-md-3"><input type="text"     name="Prenom"     class="form-control" placeholder="Prenom"     required></div>
            <div class="col-md-3"><input type="email"    name="Email"      class="form-control" placeholder="Email"      required></div>
            <div class="col-md-2"><input type="password" name="MotDePasse" class="form-control" placeholder="Mot de passe" required></div>
            <div class="col-md-1"><button type="submit"  class="btn btn-success">+ Ajouter</button></div>
          </div>
        </form>
      </div>
    </div>

    <!-- Tableau des utilisateurs -->
    <div class="card">
      <div class="card-body p-0">
        <table class="table table-hover mb-0">
          <thead class="table-dark">
            <tr>
              <th>ID</th><th>Nom</th><th>Prenom</th><th>Email</th><th>Rôle</th><th>Actions</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($users as $user): ?>
            <tr>
              <td><?= $user['ID'] ?></td>
              <td><?= htmlspecialchars($user['Nom']) ?></td>
              <td><?= htmlspecialchars($user['Prenom']) ?></td>
              <td><?= htmlspecialchars($user['Email']) ?></td>
              <td><?= $user['Role_ID'] == 1 ? 'Admin' : 'Utilisateur' ?></td>
              <td>
                <!-- Modifier -->
                <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                        data-bs-target="#editModal<?= $user['ID'] ?>">✏️</button>
                <!-- Supprimer -->
                <form method="POST" style="display:inline">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id"     value="<?= $user['ID'] ?>">
                  <button type="submit" class="btn btn-sm btn-danger"
                          onclick="return confirm('Êtes-vous sûr ?')">🗑️</button>
                </form>
              </td>
            </tr>

            <!-- Modal de modification -->
            <div class="modal fade" id="editModal<?= $user['ID'] ?>" tabindex="-1">
              <div class="modal-dialog"><div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Modifier #<?= $user['ID'] ?></h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                  <div class="modal-body">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id"     value="<?= $user['ID'] ?>">
                    <div class="mb-3">
                      <label>Nom</label>
                      <input type="text" name="Nom" class="form-control"
                             value="<?= htmlspecialchars($user['Nom']) ?>" required>
                    </div>
                    <div class="mb-3">
                      <label>Prenom</label>
                      <input type="text" name="Prenom" class="form-control"
                             value="<?= htmlspecialchars($user['Prenom']) ?>" required>
                    </div>
                    <div class="mb-3">
                      <label>Email</label>
                      <input type="email" name="Email" class="form-control"
                             value="<?= htmlspecialchars($user['Email']) ?>" required>
                    </div>
                    <div class="mb-3">
                      <label>Rôle</label>
                      <select name="Role_ID" class="form-select">
                        <option value="1" <?= $user['Role_ID']==1?'selected':'' ?>>Admin</option>
                        <option value="2" <?= $user['Role_ID']==2?'selected':'' ?>>Utilisateur</option>
                      </select>
                    </div>
                    <div class="mb-3">
                      <label>Nouveau mot de passe</label>
                      <input type="password" name="MotDePasse" class="form-control"
                             placeholder="Laisser vide pour ne pas toucher">
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Sauvegarder</button>
                  </div>
                </form>
              </div></div>
            </div>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
