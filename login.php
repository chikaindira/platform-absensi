<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg w-96">
        <h2 class="text-2xl font-bold text-center mb-4">Login Admin</h2>
        <form action="proses_login.php" method="POST" class="space-y-4">
            <input type="text" name="username" placeholder="Username" required class="border p-2 rounded w-full">
            <input type="password" name="password" placeholder="Password" required class="border p-2 rounded w-full">
            <button type="submit" class="bg-blue-600 px-4 py-2 text-white rounded w-full hover:bg-blue-800">Login</button>
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
