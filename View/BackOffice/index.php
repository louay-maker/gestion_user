<?php
require_once dirname(__DIR__) . '/../controller/FunctionsController.php';
require_once dirname(__DIR__) . '/../config.php';

// Initialize controller
$controller = new FunctionsController($conn);

// Check login status and redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../View/FrontOffice/login.php");
    exit();
}

$userType = $_SESSION['user_type'] ?? 'user';
$currentUser = $controller->getUserProfile($_SESSION['user_id']);

// Handle logout
if (isset($_GET['logout'])) {
    $controller->logout();
    header("Location: ../../View/FrontOffice/login.php");
    exit();
}

// Get dashboard data - only include tables that exist
$stats = [
    'utilisateurs' => htmlspecialchars($controller->countRecords('utilisateur')),
    'roles' => htmlspecialchars($controller->countRecords('role'))
];

$recentUsers = $controller->getRecentUsers(5);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Dashboard - StartUp Connect</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    
    <!-- Favicon -->
    <link href="../../img/logo.svg" rel="icon" type="image/svg+xml">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icon Library -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Custom Styles -->
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #1e40af;
            --background-light: #f8fafc;
            --text-dark: #1e293b;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--background-light);
            color: var(--text-dark);
            margin: 0;
            padding: 0;
        }

        .dashboard-wrapper {
            display: grid;
            grid-template-columns: 240px 1fr;
            min-height: 100vh;
        }

        .sidebar {
            background: white;
            box-shadow: 2px 0 8px rgba(0,0,0,0.05);
            padding: 1.5rem;
            position: fixed;
            width: 240px;
            height: 100vh;
            box-sizing: border-box;
        }

        .main-content {
            padding: 2rem;
            margin-left: 240px;
            max-width: 1400px;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            transition: transform 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
        }

        .data-table {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: #ef4444;
            color: white;
            border-radius: 9999px;
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }

        .mb-8 {
            margin-bottom: 2rem;
        }

        .flex {
            display: flex;
        }

        .items-center {
            align-items: center;
        }

        .justify-between {
            justify-content: space-between;
        }

        .gap-2 {
            gap: 0.5rem;
        }

        .gap-3 {
            gap: 0.75rem;
        }

        .gap-4 {
            gap: 1rem;
        }

        .text-2xl {
            font-size: 1.5rem;
        }

        .font-bold {
            font-weight: 700;
        }

        .font-semibold {
            font-weight: 600;
        }

        .font-medium {
            font-weight: 500;
        }

        .text-sm {
            font-size: 0.875rem;
        }

        .text-gray-600 {
            color: #4b5563;
        }

        .text-blue-500 {
            color: #3b82f6;
        }

        .text-blue-700 {
            color: #1d4ed8;
        }

        .text-red-500 {
            color: #ef4444;
        }

        .text-red-600 {
            color: #dc2626;
        }

        .text-red-700 {
            color: #b91c1c;
        }

        .text-green-600 {
            color: #059669;
        }

        .bg-blue-50 {
            background-color: #eff6ff;
        }

        .bg-gray-50 {
            background-color: #f9fafb;
        }

        .bg-gray-100 {
            background-color: #f3f4f6;
        }

        .bg-blue-100 {
            background-color: #dbeafe;
        }

        .bg-red-50 {
            background-color: #fef2f2;
        }

        .bg-green-50 {
            background-color: #ecfdf5;
        }

        .text-blue-800 {
            color: #1e40af;
        }

        .text-gray-800 {
            color: #1f2937;
        }

        .rounded-lg {
            border-radius: 0.5rem;
        }

        .rounded-full {
            border-radius: 9999px;
        }

        .p-2 {
            padding: 0.5rem;
        }

        .p-3 {
            padding: 0.75rem;
        }

        .p-4 {
            padding: 1rem;
        }

        .px-2 {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }

        .px-4 {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .py-1 {
            padding-top: 0.25rem;
            padding-bottom: 0.25rem;
        }

        .py-2 {
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
        }

        .block {
            display: block;
        }

        .w-full {
            width: 100%;
        }

        .w-32 {
            width: 8rem;
        }

        .hover\:bg-gray-100:hover {
            background-color: #f3f4f6;
        }

        .hover\:bg-red-50:hover {
            background-color: #fef2f2;
        }

        .hover\:bg-blue-600:hover {
            background-color: #2563eb;
        }

        .hover\:bg-green-600:hover {
            background-color: #059669;
        }

        .hover\:text-blue-700:hover {
            color: #1d4ed8;
        }

        .hover\:text-red-700:hover {
            color: #b91c1c;
        }

        .space-y-2 > * + * {
            margin-top: 0.5rem;
        }

        .border-b {
            border-bottom-width: 1px;
            border-bottom-style: solid;
            border-bottom-color: #e5e7eb;
        }

        .overflow-x-auto {
            overflow-x: auto;
        }

        .grid {
            display: grid;
        }

        .grid-cols-1 {
            grid-template-columns: repeat(1, minmax(0, 1fr));
        }

        .position-relative {
            position: relative;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.5rem;
            font-weight: 500;
            padding: 0.5rem 1rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-blue {
            background-color: #3b82f6;
            color: white;
        }

        .btn-blue:hover {
            background-color: #2563eb;
        }

        .btn-green {
            background-color: #10b981;
            color: white;
        }

        .btn-green:hover {
            background-color: #059669;
        }

        .ml-2 {
            margin-left: 0.5rem;
        }

        .mr-2 {
            margin-right: 0.5rem;
        }

        @media (min-width: 768px) {
            .md\:grid-cols-2 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 768px) {
            .dashboard-wrapper {
                grid-template-columns: 1fr;
            }
            .sidebar {
                position: static;
                width: 100%;
                height: auto;
            }
            .main-content {
                margin-left: 0;
            }
            .mobile-hidden {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="dashboard-wrapper">
        <!-- Side Navigation -->
        <aside class="sidebar">
            <div class="mb-8">
                <img src="../../img/logo.svg" alt="Logo" class="w-32">
            </div>
            
            <div class="mb-8 flex items-center gap-3">
                <div class="user-avatar" style="background-color: #dbeafe; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-user text-blue-500"></i>
                </div>
                <div>
                    <p class="font-medium"><?= htmlspecialchars($currentUser['Nom'] ?? 'User') ?></p>
                    <span class="text-sm text-gray-600"><?= ucfirst(htmlspecialchars($userType)) ?></span>
                </div>
            </div>

            <nav class="space-y-2">
                <a href="index.php" class="block p-2 rounded-lg bg-blue-50 text-blue-700">
                    <i class="fas fa-chart-line mr-2"></i>Dashboard
                </a>
                <a href="gestionUtilisateurs.php" class="block p-2 rounded-lg hover:bg-gray-100">
                    <i class="fas fa-users mr-2"></i>Utilisateurs
                </a>
                <a href="statistiques.php" class="block p-2 rounded-lg hover:bg-gray-100">
                    <i class="fas fa-chart-bar mr-2"></i>Statistiques
                </a>
                <a href="?logout=1" class="block p-2 rounded-lg text-red-600 hover:bg-red-50">
                    <i class="fas fa-sign-out-alt mr-2"></i>Déconnexion
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="mb-8 flex justify-between items-center">
                <h1 class="text-2xl font-bold">Tableau de Bord</h1>
                <div class="flex items-center gap-4">
                    <button id="export-pdf" class="btn btn-green">
                        <i class="fas fa-file-pdf mr-2"></i>Exporter PDF
                    </button>
                    <a href="statistiques.php" class="btn btn-blue">
                        <i class="fas fa-chart-bar mr-2"></i>Statistiques
                    </a>
                    <div style="position: relative;">
                        <button class="p-2 hover:bg-gray-100 rounded-lg">
                            <i class="fas fa-bell"></i>
                        </button>
                        <span class="notification-badge">3</span>
                    </div>
                </div>
            </header>

            <!-- Statistics Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                <?php foreach ($stats as $key => $value): ?>
                <div class="stat-card">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-600 text-sm"><?= ucfirst(htmlspecialchars($key)) ?></p>
                            <p class="text-2xl font-bold"><?= number_format($value) ?></p>
                        </div>
                        <i class="fas fa-<?= $key === 'utilisateurs' ? 'users' : 'user-tag' ?> text-2xl text-blue-500"></i>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Recent Activity Section -->
            <div class="grid grid-cols-1 gap-6">
                <!-- Recent Users -->
                <section class="data-table">
                    <div class="p-4 border-b flex justify-between items-center">
                        <h2 class="font-semibold">Derniers Utilisateurs</h2>
                        <button id="export-users-pdf" class="btn btn-green text-sm">
                            <i class="fas fa-file-pdf mr-2"></i>Exporter
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full" id="users-table">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="p-3 text-left text-sm">Nom</th>
                                    <th class="p-3 text-left text-sm">Prénom</th>
                                    <th class="p-3 text-left text-sm">Email</th>
                                    <th class="p-3 text-left text-sm">Rôle</th>
                                    <th class="p-3 text-left text-sm">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($recentUsers)): ?>
                                <tr>
                                    <td colspan="5" class="p-3 text-center">Aucun utilisateur trouvé</td>
                                </tr>
                                <?php else: ?>
                                    <?php foreach ($recentUsers as $user): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="p-3">
                                            <?= htmlspecialchars($user['Nom']) ?>
                                        </td>
                                        <td class="p-3">
                                            <?= htmlspecialchars($user['Prenom']) ?>
                                        </td>
                                        <td class="p-3">
                                            <?= htmlspecialchars($user['Email']) ?>
                                        </td>
                                        <td class="p-3">
                                            <span class="px-2 py-1 rounded-full text-sm <?= htmlspecialchars($user['Role_ID']) == 2 ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' ?>">
                                                <?= htmlspecialchars($user['NomRole']) ?>
                                            </span>
                                        </td>
                                        <td class="p-3">
                                            <div class="flex gap-2">
                                                <a href="editUser.php?id=<?= htmlspecialchars($user['ID']) ?>" 
                                                class="text-blue-500 hover:text-blue-700">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="deleteUser.php?id=<?= htmlspecialchars($user['ID']) ?>" 
                                                class="text-red-500 hover:text-red-700 delete-btn"
                                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <!-- Include jsPDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
    
    <script>
        // Responsive menu toggle
        function toggleMenu() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.classList.toggle('mobile-hidden');
        }

        // Confirm before critical actions
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                if (!confirm('Cette action est irréversible. Continuer ?')) {
                    e.preventDefault();
                }
            });
        });

        // PDF Export functionality
        document.addEventListener('DOMContentLoaded', function() {
            const { jsPDF } = window.jspdf;

            // Export full dashboard to PDF
            document.getElementById('export-pdf').addEventListener('click', function() {
                const doc = new jsPDF();
                
                // Add title
                doc.setFontSize(18);
                doc.text('Dashboard - StartUp Connect', 14, 22);
                
                // Add stats
                doc.setFontSize(14);
                doc.text('Statistiques', 14, 35);
                
                <?php foreach ($stats as $key => $value): ?>
                doc.setFontSize(12);
                doc.text('<?= ucfirst(htmlspecialchars($key)) ?>: <?= number_format($value) ?>', 20, <?= 40 + 10 * array_search($key, array_keys($stats)) ?>);
                <?php endforeach; ?>
                
                // Add users table
                doc.setFontSize(14);
                doc.text('Derniers Utilisateurs', 14, 70);
                
                // Create table data
                const tableColumn = ["Nom", "Prénom", "Email", "Rôle"];
                const tableRows = [];
                
                <?php foreach ($recentUsers as $user): ?>
                tableRows.push([
                    "<?= htmlspecialchars($user['Nom']) ?>", 
                    "<?= htmlspecialchars($user['Prenom']) ?>", 
                    "<?= htmlspecialchars($user['Email']) ?>", 
                    "<?= htmlspecialchars($user['NomRole']) ?>"
                ]);
                <?php endforeach; ?>
                
                // Generate table
                doc.autoTable({
                    head: [tableColumn],
                    body: tableRows,
                    startY: 75,
                });
                
                // Save PDF
                doc.save('dashboard-report.pdf');
            });
            
            // Export just users table to PDF
            document.getElementById('export-users-pdf').addEventListener('click', function() {
                const doc = new jsPDF();
                
                // Add title
                doc.setFontSize(18);
                doc.text('Liste des Utilisateurs', 14, 22);
                
                // Create table data
                const tableColumn = ["Nom", "Prénom", "Email", "Rôle"];
                const tableRows = [];
                
                <?php foreach ($recentUsers as $user): ?>
                tableRows.push([
                    "<?= htmlspecialchars($user['Nom']) ?>", 
                    "<?= htmlspecialchars($user['Prenom']) ?>", 
                    "<?= htmlspecialchars($user['Email']) ?>", 
                    "<?= htmlspecialchars($user['NomRole']) ?>"
                ]);
                <?php endforeach; ?>
                
                // Generate table
                doc.autoTable({
                    head: [tableColumn],
                    body: tableRows,
                    startY: 30,
                });
                
                // Save PDF
                doc.save('users-list.pdf');
            });
        });
    </script>
</body>
</html>