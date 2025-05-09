<?php
// Include required files with absolute paths
$baseDir = dirname(dirname(__DIR__));
require_once($baseDir . '/Controller/usercontroller.php'); // Fixed case sensitivity
require_once($baseDir . '/Model/config.php');
require_once($baseDir . '/Model/model.php');

// Get statistics from UserController
$userController = new UserController(); // Fixed case sensitivity
$totalUsers = $userController->countUsers();

// Get gender statistics
$maleUsers = $userController->countUsersByGender('Homme'); 
$femaleUsers = $userController->countUsersByGender('Femme');

// Get active vs inactive users
$activeUsers = $userController->countActiveUsers();
$inactiveUsers = $userController->countInactiveUsers();

// Get registrations over time (last 7 days)
$recentRegistrations = $userController->getRecentRegistrations(7);
?>

<!-- Statistics Cards -->
<div class="container-fluid py-5">
    <div class="row g-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Total Users</h6>
                            <h2 class="mb-0"><?php echo $totalUsers; ?></h2>
                        </div>
                        <i class="fas fa-users fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Active Users</h6>
                            <h2 class="mb-0"><?php echo $activeUsers; ?></h2>
                        </div>
                        <i class="fas fa-check-circle fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Male Users</h6>
                            <h2 class="mb-0"><?php echo $maleUsers; ?></h2>
                        </div>
                        <i class="fas fa-male fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Female Users</h6>
                            <h2 class="mb-0"><?php echo $femaleUsers; ?></h2>
                        </div>
                        <i class="fas fa-female fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row mt-4">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Gender Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="genderChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">User Activity Status</h5>
                </div>
                <div class="card-body">
                    <canvas id="activityChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">New Registrations (Last 7 Days)</h5>
                </div>
                <div class="card-body">
                    <canvas id="registrationChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Gender Distribution Chart
const genderCtx = document.getElementById('genderChart').getContext('2d');
new Chart(genderCtx, {
    type: 'pie',
    data: {
        labels: ['Male', 'Female'],
        datasets: [{
            data: [<?php echo $maleUsers; ?>, <?php echo $femaleUsers; ?>],
            backgroundColor: ['#36A2EB', '#FF6384']
        }]
    }
});

// Activity Status Chart
const activityCtx = document.getElementById('activityChart').getContext('2d');
new Chart(activityCtx, {
    type: 'doughnut',
    data: {
        labels: ['Active', 'Inactive'],
        datasets: [{
            data: [<?php echo $activeUsers; ?>, <?php echo $inactiveUsers; ?>],
            backgroundColor: ['#4CAF50', '#FF9800']
        }]
    }
});

// Registration Timeline Chart
const registrationCtx = document.getElementById('registrationChart').getContext('2d');
new Chart(registrationCtx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode(array_keys($recentRegistrations)); ?>,
        datasets: [{
            label: 'New Registrations',
            data: <?php echo json_encode(array_values($recentRegistrations)); ?>,
            borderColor: '#2196F3',
            tension: 0.1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});
</script>
