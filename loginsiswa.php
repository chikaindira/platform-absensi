<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Siswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg w-96">
        <h2 class="text-2xl font-bold text-center mb-4">Login Siswa</h2>
        <form action="proses_loginsiswa.php" method="POST" class="space-y-4">
            <div class="space-y-2">
                <label for="username" class="block text-sm font-medium text-gray-700">NIS</label>
                <input type="text" 
                       id="username" 
                       name="username" 
                       placeholder="Masukkan NIS" 
                       required 
                       class="border p-2 rounded w-full focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="space-y-2">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       placeholder="Masukkan password" 
                       required 
                       class="border p-2 rounded w-full focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <button type="submit" 
                    class="w-full bg-blue-600 text-white rounded-lg px-4 py-2 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Login
            </button>
        </form>
        <!-- Tombol Kembali -->
        <a href="index.html" 
           class="group inline-flex items-center px-4 py-2 rounded-lg
                  text-indigo-600 hover:text-white
                  bg-indigo-50 hover:bg-indigo-600
                  transition-all duration-300 ease-in-out
                  mb-6 -ml-2">
            <i class="fas fa-arrow-left mr-2 transform group-hover:-translate-x-1 transition-transform duration-300"></i>
            <span class="font-medium">Kembali ke Beranda</span>
        </a>
    </div>
</body>
</html>