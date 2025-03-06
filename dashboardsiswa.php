<?php
session_start();
include 'config.php';

// Cek login
if (!isset($_SESSION['siswa'])) {
    header("Location: login_siswa.php");
    exit();
}

$nis = $_SESSION['siswa'];

// Ambil data siswa
$stmt = $conn->prepare("SELECT * FROM siswa WHERE nis = ?");
$stmt->bind_param("s", $nis);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $siswa = $result->fetch_assoc();
    $siswa_id = $siswa['id'];
    
    // Ambil data kehadiran hari ini
    $tanggal_hari_ini = date('Y-m-d');
    $stmt = $conn->prepare("SELECT status FROM absensi WHERE siswa_id = ? AND tanggal = ?");
    $stmt->bind_param("ss", $siswa_id, $tanggal_hari_ini);
    $stmt->execute();
    $result = $stmt->get_result();
    $status_kehadiran = ($result && $result->num_rows > 0) ? $result->fetch_assoc()['status'] : 'Belum Absen';
    
    // Ambil rekap 30 hari terakhir
    $tanggal_awal = date('Y-m-d', strtotime('-30 days'));
    $stmt = $conn->prepare("SELECT status, COUNT(*) as jumlah FROM absensi WHERE siswa_id = ? AND tanggal >= ? GROUP BY status");
    $stmt->bind_param("ss", $siswa_id, $tanggal_awal);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $rekap_kehadiran = [
        'Hadir' => 0,
        'Sakit' => 0,
        'Terlambat' => 0,
        'Alpha' => 0
    ];
    
    while ($row = $result->fetch_assoc()) {
        $rekap_kehadiran[$row['status']] = $row['jumlah'];
    }
    
} else {
    echo "Data siswa tidak ditemukan.";
    exit();
}

$pengumuman = "Pengumuman: Libur bulan Ramadhan dimulai tanggal 27 Februari 2025.";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #f6f8fc 0%, #f0f3f9 100%);
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.15);
        }
        
        .stat-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
        }
        
        .nav-link {
            transition: all 0.3s ease;
        }
        
        .nav-link:hover {
            background: rgba(99, 102, 241, 0.1);
            transform: translateX(5px);
        }
        
        .nav-link.active {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(99, 102, 241, 0.2) 100%);
            border-right: 3px solid #6366f1;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fadeIn {
            animation: fadeIn 0.5s ease-out forwards;
        }

        .gradient-border {
            position: relative;
            border-radius: 1rem;
            background: linear-gradient(white, white) padding-box,
                        linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%) border-box;
            border: 2px solid transparent;
        }
    </style>
</head>
<body class="min-h-screen">
    <!-- Navbar -->
    <nav class="glass-card sticky top-0 z-50 border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-4">
                    <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold">C</span>
                    </div>
                    <span class="text-xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                        Dashboard Siswa
                    </span>
                </div>
                <div class="flex items-center gap-6">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white">
                            <?= strtoupper(substr($siswa['nama'], 0, 1)) ?>
                        </div>
                        <span class="text-sm font-medium text-gray-700"><?= htmlspecialchars($siswa['nama']) ?></span>
                    </div>
                    <a href="logout_siswa.php" class="bg-gradient-to-r from-red-500 to-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:from-red-600 hover:to-red-700 transition-all duration-300 shadow-lg shadow-red-500/30">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 glass-card min-h-screen p-4 border-r border-gray-200">
            <div class="space-y-2">
                <a href="#" class="nav-link active flex items-center space-x-3 p-3 rounded-xl">
                    <i class="fas fa-home text-indigo-600"></i>
                    <span class="font-medium">Dashboard</span>
                </a>
                <a href="profilsiswa.php" class="nav-link flex items-center space-x-3 p-3 rounded-xl text-gray-600">
                    <i class="fas fa-user"></i>
                    <span>Profil</span>
                </a>
                <a href="jadwalsiswa.php" class="nav-link flex items-center space-x-3 p-3 rounded-xl text-gray-600">
                    <i class="fas fa-calendar"></i>
                    <span>Jadwal</span>
                </a>

            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8">
            <div class="max-w-7xl mx-auto space-y-8">
                <!-- Welcome Section -->
<div class="glass-card p-6 rounded-2xl animate-fadeIn gradient-border">
    <h1 class="text-3xl font-bold text-gray-800">
        <?php 
        $hour = date('H');
        $greeting = 'Selamat ';
        if($hour < 11) {
            $greeting .= 'Pagi';
        } elseif($hour < 15) {
            $greeting .= 'Siang';
        } elseif($hour < 19) {
            $greeting .= 'Sore';
        } else {
            $greeting .= 'Malam';
        }
        echo 'Selamat Siang, ' . htmlspecialchars($siswa['nama']) . '!';
        ?>
    </h1>
</div>

                <!-- Status Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Hadir -->
                    <div class="stat-card rounded-2xl overflow-hidden">
                        <div class="bg-gradient-to-br from-emerald-400 to-emerald-600 p-6 text-white">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm font-medium opacity-80">Hadir</p>
                                    <p class="text-3xl font-bold mt-2"><?= $rekap_kehadiran['Hadir'] ?></p>
                                </div>
                                <div class="bg-white/20 p-3 rounded-xl">
                                    <i class="fas fa-check-circle text-2xl"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sakit -->
                    <div class="stat-card rounded-2xl overflow-hidden">
                        <div class="bg-gradient-to-br from-blue-400 to-blue-600 p-6 text-white">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm font-medium opacity-80">Sakit</p>
                                    <p class="text-3xl font-bold mt-2"><?= $rekap_kehadiran['Sakit'] ?></p>
                                </div>
                                <div class="bg-white/20 p-3 rounded-xl">
                                    <i class="fas fa-hospital text-2xl"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Terlambat -->
                    <div class="stat-card rounded-2xl overflow-hidden">
                        <div class="bg-gradient-to-br from-amber-400 to-amber-600 p-6 text-white">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm font-medium opacity-80">Terlambat</p>
                                    <p class="text-3xl font-bold mt-2"><?= $rekap_kehadiran['Terlambat'] ?></p>
                                </div>
                                <div class="bg-white/20 p-3 rounded-xl">
                                    <i class="fas fa-clock text-2xl"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Alpha -->
                    <div class="stat-card rounded-2xl overflow-hidden">
                        <div class="bg-gradient-to-br from-rose-400 to-rose-600 p-6 text-white">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm font-medium opacity-80">Alpha</p>
                                    <p class="text-3xl font-bold mt-2"><?= $rekap_kehadiran['Alpha'] ?></p>
                                </div>
                                <div class="bg-white/20 p-3 rounded-xl">
                                    <i class="fas fa-times-circle text-2xl"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chart & Announcement -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Chart -->
                    <div class="glass-card p-6 rounded-2xl">
                        <h3 class="text-lg font-semibold text-gray-800 mb-6 flex items-center">
                            <i class="fas fa-chart-pie mr-2 text-indigo-600"></i>
                            Statistik Kehadiran
                        </h3>
                        <canvas id="kehadiranChart" class="p-2"></canvas>
                    </div>

                    <!-- Announcement -->
                    <div class="glass-card p-6 rounded-2xl">
                        <h3 class="text-lg font-semibold text-gray-800 mb-6 flex items-center">
                            <i class="fas fa-bullhorn mr-2 text-indigo-600"></i>
                            Pengumuman Terbaru
                        </h3>
                        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 border-l-4 border-indigo-500 p-6 rounded-xl">
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-calendar text-indigo-600"></i>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="text-base font-semibold text-gray-800">Informasi Libur</h4>
                                    <p class="mt-1 text-gray-600"><?= htmlspecialchars($pengumuman) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Initialize Chart
        const ctx = document.getElementById('kehadiranChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Hadir', 'Sakit', 'Terlambat', 'Alpha'],
                datasets: [{
                    data: [
                        <?= $rekap_kehadiran['Hadir'] ?>,
                        <?= $rekap_kehadiran['Sakit'] ?>,
                        <?= $rekap_kehadiran['Terlambat'] ?>,
                        <?= $rekap_kehadiran['Alpha'] ?>
                    ],
                    backgroundColor: [
                        'rgba(34, 197, 94, 0.9)',
                        'rgba(59, 130, 246, 0.9)',
                        'rgba(234, 179, 8, 0.9)',
                        'rgba(239, 68, 68, 0.9)'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
                },
                cutout: '70%'
            }
        });
    </script>
</body>
</html>