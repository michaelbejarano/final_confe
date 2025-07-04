<!-- resources/views/components/app-layout.blade.php -->

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Sistema</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js']) <!-- Si usas Vite -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>
<body class="bg-[#1f212e] text-white">

    <div class="flex min-h-screen">

        <!-- Sidebar -->
        <aside class="w-64 bg-[#232733] flex flex-col py-8 px-6">
            <div class="flex items-center gap-2 text-white text-2xl font-bold mb-10">
                <i class='bx bx-rocket'></i> <span>Sistema</span>
            </div>

            <nav class="flex flex-col gap-3">
                <a href="{{ route('dashboard') }}" class="hover:bg-[#353a4a] px-4 py-2 rounded transition">Dashboard</a>
                <a href="#" class="hover:bg-[#353a4a] px-4 py-2 rounded transition">Clientes</a>
                <a href="#" class="hover:bg-[#353a4a] px-4 py-2 rounded transition">Productos</a>
                <a href="#" class="hover:bg-[#353a4a] px-4 py-2 rounded transition">Facturación</a>
                <a href="#" class="hover:bg-[#353a4a] px-4 py-2 rounded transition">Reportes</a>
            </nav>

            <form method="POST" action="{{ route('logout') }}" class="mt-auto pt-6 border-t border-[#353a4a]">
                @csrf
                <button class="w-full text-left px-4 py-2 hover:bg-[#353a4a] rounded transition text-[#b0b3c7] hover:text-red-400">
                    <i class='bx bx-log-out'></i> Cerrar sesión
                </button>
            </form>
        </aside>

        <!-- Aquí irá el contenido de cada página -->
        <main class="flex-1 p-10">
            {{ $slot }}
        </main>

    </div>
</body>
</html>
