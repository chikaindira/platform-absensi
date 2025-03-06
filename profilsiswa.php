<?php
session_start();
include 'config.php';

// Cek apakah siswa sudah login
if (!isset($_SESSION['siswa'])) {
    header("Location: login_siswa.php");
    exit();
}

// Ambil data siswa dari database
$nis = $_SESSION['siswa'];
$siswa_result = $conn->query("SELECT * FROM siswa WHERE nis = '$nis'");

// Pastikan query berhasil
if (!$siswa_result) {
    die("Query error: " . $conn->error);
}

$siswa = $siswa_result->fetch_assoc();

// Pastikan data siswa ditemukan
if (!$siswa) {
    echo "<div class='text-red-500'>Data siswa tidak ditemukan.</div>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Siswa</title>
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
                <a href="dashboardsiswa.php" class="nav-link flex items-center space-x-3 p-3 rounded-xl text-gray-600 hover:bg-indigo-100">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
                <a href="profilsiswa.php" class="nav-link flex items-center space-x-3 p-3 rounded-xl text-gray-600 active">
                    <i class="fas fa-user"></i>
                    <span>Profil</span>
                </a>
                <a href="jadwalsiswa.php" class="nav-link flex items-center space-x-3 p-3 rounded-xl text-gray-600 hover:bg-indigo-100">
                    <i class="fas fa-calendar"></i>
                    <span>Jadwal</span>
                </a>
                
            </div>
        </aside>

        <!-- Content -->
        <main class="flex-1 p-8">
            <h2 class="text-3xl font-bold mb-8 text-slate-800 animate-fadeIn">Profil Siswa</h2>

            <!-- Modern Profile Card -->
            <div class="glass-card rounded-2xl shadow-xl p-8 border border-slate-100 animate-fadeIn">
                <!-- Header dengan gradien -->
                <div class="bg-gradient-to-r from-blue-500 to-purple-500 rounded-t-2xl h-24"></div>

                <div class="relative">
                    <div class="flex items-center space-x-6 mb-6 pt-4 pl-4">
                        <!-- Foto Profil -->
                        <div class="w-24 h-24 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden">
                        <?php
                            $foto_profil = "uploads/chika.jpg";
                            if (file_exists($foto_profil)) {
                                echo '<img src="' . $foto_profil . '" alt="Foto Profil" class="w-full h-full object-cover">';
                            } else {
                                }
                        ?>
                        </div>

                        <div>
                            <h3 class="text-2xl font-semibold text-gray-800"><?php echo htmlspecialchars($siswa['nama']); ?></h3>
                            <p class="text-gray-600">NIS: <?php echo htmlspecialchars($siswa['nis']); ?></p>
                        </div>
                    </div>

                    <!-- Informasi Profil dengan Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 px-4">
                        <div>
                            <i class="fas fa-graduation-cap mr-2 text-indigo-500"></i>
                            <strong class="block font-medium text-slate-700 mb-1">Kelas:</strong>
                            <p class="text-slate-600">XI RPL</p>
                        </div>
                        <div>
                            <i class="fas fa-envelope mr-2 text-indigo-500"></i>
                            <strong class="block font-medium text-slate-700 mb-1">Email:</strong>
                            <p class="text-slate-600">
                                <?php
                                if (isset($siswa['email']) && !empty($siswa['email'])) {
                                    echo htmlspecialchars($siswa['email']);
                                } else {
                                    echo "<span class='text-gray-500'>Email tidak tersedia</span>";
                                }
                                ?>
                            </p>
                        </div>
                        <div>
                            <i class="fas fa-calendar-alt mr-2 text-indigo-500"></i>
                            <strong class="block font-medium text-slate-700 mb-1">Tanggal Lahir:</strong>
                            <p class="text-slate-600">20 Juni 2008</p>
                        </div>
                        <div>
                            <i class="fas fa-map-marker-alt mr-2 text-indigo-500"></i>
                            <strong class="block font-medium text-slate-700 mb-1">Alamat:</strong>
                            <p class="text-slate-600">Jl. Rawamangun</p>
                        </div>
                    </div>

                </div>
            </div>
        </main>
    </div>
</body>
</html>
