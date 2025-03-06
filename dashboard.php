<?php
session_start();
include 'config.php';
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Ambil data dari database
$total_siswa_result = $conn->query("SELECT COUNT(*) AS total FROM siswa");
$total_siswa = $total_siswa_result->fetch_assoc()['total'];
$total_guru_result = $conn->query("SELECT COUNT(*) AS total FROM guru");
$total_guru = $total_guru_result->fetch_assoc()['total'];
$hadir_result = $conn->query("SELECT COUNT(*) AS hadir FROM absensi WHERE status='Hadir'");
$hadir = $hadir_result->fetch_assoc()['hadir'];
$sakit_result = $conn->query("SELECT COUNT(*) AS sakit FROM absensi WHERE status='Sakit'");
$sakit = $sakit_result->fetch_assoc()['sakit'];
$terlambat_result = $conn->query("SELECT COUNT(*) AS terlambat FROM absensi WHERE status='Terlambat'");
$terlambat = $terlambat_result->fetch_assoc()['terlambat'];
$alpha_result = $conn->query("SELECT COUNT(*) AS alpha FROM absensi WHERE status='Alpha'");
$alpha = $alpha_result->fetch_assoc()['alpha'];
$tidak_hadir = $sakit + $terlambat + $alpha;

// Add these queries after your existing student attendance queries
$guru_hadir_result = $conn->query("SELECT COUNT(*) AS hadir FROM absensi_guru WHERE status='Hadir'");
$guru_hadir = $guru_hadir_result->fetch_assoc()['hadir'];

$guru_sakit_result = $conn->query("SELECT COUNT(*) AS sakit FROM absensi_guru WHERE status='Sakit'");
$guru_sakit = $guru_sakit_result->fetch_assoc()['sakit'];

$guru_terlambat_result = $conn->query("SELECT COUNT(*) AS terlambat FROM absensi_guru WHERE status='Terlambat'");
$guru_terlambat = $guru_terlambat_result->fetch_assoc()['terlambat'];

$guru_alpha_result = $conn->query("SELECT COUNT(*) AS alpha FROM absensi_guru WHERE status='Alpha'");
$guru_alpha = $guru_alpha_result->fetch_assoc()['alpha'];

$guru_tidak_hadir = $guru_sakit + $guru_terlambat + $guru_alpha;

// Add these percentage calculations
$persentase_guru_hadir = $total_guru > 0 ? round(($guru_hadir / $total_guru) * 100, 1) : 0;
$persentase_guru_tidak_hadir = $total_guru > 0 ? round(($guru_tidak_hadir / $total_guru) * 100, 1) : 0;

// Tambahan perhitungan persentase
$persentase_hadir = $total_siswa > 0 ? round(($hadir / $total_siswa) * 100, 1) : 0;
$persentase_tidak_hadir = $total_siswa > 0 ? round(($tidak_hadir / $total_siswa) * 100, 1) : 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Absensi Siswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f0f4f8;
        }
        .gradient-card-1 {
            background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%); /* Indigo gradient */
        }
        .gradient-card-2 {
            background: linear-gradient(135deg, #22c55e 0%, #15803d 100%); /* Green gradient */
        }
        .gradient-card-3 {
            background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%); /* Red gradient */
        }
        .hover-scale:hover {
            transform: scale(1.05);
            transition: transform 0.3s ease-in-out;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
        }
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
    </style>
</head>
<body class="bg-slate-50">
     <!-- Navbar -->
     <nav class="bg-[#534FDE] text-white p-4 shadow-sm flex justify-between items-center">
        <span class="text-xl font-bold tracking-tight">SMKN 40 JAKARTA</span>
        <a href="#" onclick="confirmLogout()" 
           class="bg-red-500 px-6 py-2 rounded-lg hover:bg-red-600 text-white transition duration-200 text-sm font-semibold shadow-sm hover-scale">
            Logout
        </a>
    </nav>
    
    <div class="flex">
        <!-- Sidebar -->
<aside class="w-64 bg-[#EFF3EA] text-slate-800 min-h-screen p-5">
    <ul class="space-y-2">
        <li>
            <a href="absensi.php"
               class="block py-3 px-4 rounded-xl hover:bg-slate-700 transition duration-200 font-medium flex items-center space-x-3 hover-scale hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
                <span>Absensi Siswa</span>
            </a>
        </li>
        <li>
            <a href="absensiguru.php"
               class="block py-3 px-4 rounded-xl hover:bg-slate-700 transition duration-200 font-medium flex items-center space-x-3 hover-scale hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
                <span>Absensi Guru</span>
            </a>
        </li>
        <li>
            <a href="siswa.php"
               class="block py-3 px-4 rounded-xl hover:bg-slate-700 transition duration-200 font-medium flex items-center space-x-3 hover-scale hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <span>Data Siswa</span>
            </a>
        </li>
        <li>
            <a href="guru.php"
               class="block py-3 px-4 rounded-xl hover:bg-slate-700 transition duration-200 font-medium flex items-center space-x-3 hover-scale hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <span>Data Guru</span>
            </a>
        </li>
        <li>
            <a href="manajemen_admin.php"
               class="block py-3 px-4 rounded-xl hover:bg-slate-700 transition duration-200 font-medium flex items-center space-x-3 hover-scale hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <span>Manajemen Admin</span>
            </a>
        </li>
    </ul>
</aside>

        
        <!-- Content -->
        <main class="flex-1 p-8">
            <h2 class="text-3xl font-bold mb-8 text-slate-800">Selamat Datang di Dashboard Absensi!</h2>

            <!-- Statistik Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="gradient-card-1 p-6 rounded-2xl shadow-lg text-white hover-scale transition duration-300 relative overflow-hidden">
                    <div class="absolute top-0 right-0 opacity-30">
                        <svg class="w-32 h-32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium mb-2 opacity-90">Total Siswa</h3>
                    <p class="text-4xl font-bold"><?php echo $total_siswa; ?></p>
                    <div class="mt-4 text-sm opacity-80">Siswa SMKN 40 Jakarta</div>
                </div>
                <div class="gradient-card-1 p-6 rounded-2xl shadow-lg text-white hover-scale transition duration-300 relative overflow-hidden">
                    <div class="absolute top-0 right-0 opacity-30">
                        <svg class="w-32 h-32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium mb-2 opacity-90">Total Guru</h3>
                    <p class="text-4xl font-bold"><?php echo $total_guru; ?></p>
                    <div class="mt-4 text-sm opacity-80">Guru SMKN 40 Jakarta</div>
                </div>
                <div class="gradient-card-2 p-6 rounded-2xl shadow-lg text-white hover-scale transition duration-300 relative overflow-hidden">
                    <div class="absolute top-0 right-0 opacity-30">
                        <svg class="w-32 h-32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium mb-2 opacity-90">Hadir</h3>
                    <p class="text-4xl font-bold"><?php echo $hadir; ?></p>
                    <div class="mt-4 text-sm opacity-80"><?php echo $persentase_hadir; ?>% dari total</div>
                </div>
                <div class="gradient-card-3 p-6 rounded-2xl shadow-lg text-white hover-scale transition duration-300 relative overflow-hidden">
                    <div class="absolute top-0 right-0 opacity-30">
                        <svg class="w-32 h-32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium mb-2 opacity-90">Tidak Hadir</h3>
                    <p class="text-4xl font-bold"><?php echo $tidak_hadir; ?></p>
                    <div class="mt-4 text-sm opacity-80"><?php echo $persentase_tidak_hadir; ?>% dari total</div>
                </div>
            </div>
            
            <!-- Statistik Chart dan Detail -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Statistik Absensi Siswa -->
    <div class="md:col-span-2 bg-white p-6 rounded-2xl shadow-lg border border-slate-100">
        <h3 class="text-xl font-semibold mb-6 text-slate-800">Statistik Absensi Siswa</h3>
        <canvas id="absensiChart" class="w-full" height="300"></canvas>
    </div>
    
    <!-- Detail Absensi Siswa -->
    <div class="bg-white p-6 rounded-2xl shadow-lg border border-slate-100">
        <h3 class="text-xl font-semibold mb-6 text-slate-800">Detail Absensi Siswa</h3>
        <div class="space-y-4">
            <div>
                <span class="status-badge bg-green-100 text-green-800">Hadir</span>
                <span class="float-right font-semibold"><?php echo $hadir; ?> Siswa</span>
            </div>
            <div>
                <span class="status-badge bg-blue-100 text-blue-800">Sakit</span>
                <span class="float-right font-semibold"><?php echo $sakit; ?> Siswa</span>
            </div>
            <div>
                <span class="status-badge bg-yellow-100 text-yellow-800">Terlambat</span>
                <span class="float-right font-semibold"><?php echo $terlambat; ?> Siswa</span>
            </div>
            <div>
                <span class="status-badge bg-red-100 text-red-800">Alpha</span>
                <span class="float-right font-semibold"><?php echo $alpha; ?> Siswa</span>
            </div>
        </div>
    </div>
</div>

<!-- Statistik Absensi Guru -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
    <div class="md:col-span-2 bg-white p-6 rounded-2xl shadow-lg border border-slate-100">
        <h3 class="text-xl font-semibold mb-6 text-slate-800">Statistik Absensi Guru</h3>
        <canvas id="absensiGuruChart" class="w-full" height="300"></canvas>
    </div>
    
    <!-- Detail Absensi Guru -->
    <div class="bg-white p-6 rounded-2xl shadow-lg border border-slate-100">
        <h3 class="text-xl font-semibold mb-6 text-slate-800">Detail Absensi Guru</h3>
        <div class="space-y-4">
            <div>
                <span class="status-badge bg-green-100 text-green-800">Hadir</span>
                <span class="float-right font-semibold"><?php echo $guru_hadir; ?> Guru</span>
            </div>
            <div>
                <span class="status-badge bg-blue-100 text-blue-800">Sakit</span>
                <span class="float-right font-semibold"><?php echo $guru_sakit; ?> Guru</span>
            </div>
            <div>
                <span class="status-badge bg-yellow-100 text-yellow-800">Terlambat</span>
                <span class="float-right font-semibold"><?php echo $guru_terlambat; ?> Guru</span>
            </div>
            <div>
                <span class="status-badge bg-red-100 text-red-800">Alpha</span>
                <span class="float-right font-semibold"><?php echo $guru_alpha; ?> Guru</span>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function confirmLogout() {
        if (confirm("Apakah Anda yakin ingin logout?")) {
            window.location.href = "logout.php";
        }
    }

    const ctx = document.getElementById('absensiChart').getContext('2d');
    const absensiChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Hadir', 'Sakit', 'Terlambat', 'Alpha'],
            datasets: [{
                label: 'Jumlah Siswa',
                data: [
                    <?php echo $hadir; ?>, 
                    <?php echo $sakit; ?>, 
                    <?php echo $terlambat; ?>, 
                    <?php echo $alpha; ?>
                ],
                backgroundColor: [
                    'rgba(16, 185, 129, 0.9)',  // Green
                    'rgba(59, 130, 246, 0.9)',  // Blue
                    'rgba(245, 158, 11, 0.9)',  // Amber
                    'rgba(244, 63, 94, 0.9)'    // Rose
                ],
                borderRadius: 8,
                maxBarThickness: 50
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(148, 163, 184, 0.1)'
                    },
                    ticks: {
                        font: {
                            family: "'Plus Jakarta Sans', sans-serif"
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            family: "'Plus Jakarta Sans', sans-serif"
                        }
                    }
                }
            }
        }
    });

    // Add this after the existing absensiChart code
const ctxGuru = document.getElementById('absensiGuruChart').getContext('2d');
const absensiGuruChart = new Chart(ctxGuru, {
    type: 'bar',
    data: {
        labels: ['Hadir', 'Sakit', 'Terlambat', 'Alpha'],
        datasets: [{
            label: 'Jumlah Guru',
            data: [
                <?php echo $guru_hadir; ?>, 
                <?php echo $guru_sakit; ?>, 
                <?php echo $guru_terlambat; ?>, 
                <?php echo $guru_alpha; ?>
            ],
            backgroundColor: [
                'rgba(16, 185, 129, 0.9)',  // Green
                'rgba(59, 130, 246, 0.9)',  // Blue
                'rgba(245, 158, 11, 0.9)',  // Amber
                'rgba(244, 63, 94, 0.9)'    // Rose
            ],
            borderRadius: 8,
            maxBarThickness: 50
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(148, 163, 184, 0.1)'
                },
                ticks: {
                    font: {
                        family: "'Plus Jakarta Sans', sans-serif"
                    }
                }
            },
            x: {
                grid: {
                    display: false
                },
                ticks: {
                    font: {
                        family: "'Plus Jakarta Sans', sans-serif"
                    }
                }
            }
        }
    }
});
</script>
</body>
</html>