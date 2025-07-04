<div x-data="{ sidebarOpen: true, userMenuOpen: false }" class="flex min-h-screen bg-[#181c24]">
    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'w-64' : 'w-20'"
           class="bg-[#232733] text-white flex flex-col py-8 px-4 min-h-screen transition-all duration-300">

        <!-- Botón Toggle -->
        <button @click="sidebarOpen = !sidebarOpen"
                class="mb-6 p-2 bg-[#353a4a] rounded hover:bg-[#4b5163] transition">
            <i class='bx bx-menu text-2xl'></i>
        </button>

        <!-- Logo o Título -->
        <div class="flex items-center mb-10" x-show="sidebarOpen">
            <span class="text-2xl font-bold tracking-widest">FACTURACIÓN</span>
        </div>

        <!-- Perfil -->
        <div class="flex items-center gap-3 mb-8 relative">
            <div class="w-12 h-12 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                <i class='bx bx-user text-white text-xl'></i>
            </div>
            <div @click="userMenuOpen = !userMenuOpen" class="cursor-pointer" x-show="sidebarOpen">
                <div class="font-semibold">{{ Auth::user()->name }}</div>
                <div class="text-xs text-[#b0b3c7]">Administrador</div>
            </div>

            <!-- Dropdown -->
            <div x-show="userMenuOpen" @click.away="userMenuOpen = false"
                 class="absolute top-14 left-0 bg-[#353a4a] rounded shadow-lg w-48 z-50">
                <a href="{{ route('profile.edit') }}"
                   class="block px-4 py-2 text-sm hover:bg-[#4b5163]">Editar Perfil</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full text-left px-4 py-2 text-sm hover:bg-[#4b5163] text-red-400">
                        Cerrar Sesión
                    </button>
                </form>
            </div>
        </div>

        <!-- Navegación -->
        <nav class="flex-1">
            <!-- Principal -->
            <div class="mb-6">
                <h3 class="text-xs font-semibold text-[#b0b3c7] uppercase tracking-wider mb-3" x-show="sidebarOpen">Principal</h3>
                <ul class="space-y-2">
                    <li class="flex items-center gap-3 text-white bg-[#353a4a] rounded-lg px-3 py-2">
                        <i class='bx bx-grid-alt'></i>
                        <span x-show="sidebarOpen">Dashboard</span>
                    </li>
                    <li class="flex items-center gap-3 text-[#b0b3c7] hover:text-white hover:bg-[#353a4a] rounded-lg px-3 py-2 cursor-pointer transition-all duration-200" onclick="window.location.href='#'">
                        <i class='bx bx-buildings'></i>
                        <span x-show="sidebarOpen">Compañías</span>
                    </li>
                    <li class="flex items-center gap-3 text-[#b0b3c7] hover:text-white hover:bg-[#353a4a] rounded-lg px-3 py-2 cursor-pointer transition-all duration-200" onclick="window.location.href='#'">
                        <i class='bx bx-receipt'></i>
                        <span x-show="sidebarOpen">Nueva Factura</span>
                    </li>
                </ul>
            </div>

            <!-- Gestión -->
            <div class="mb-6">
                <h3 class="text-xs font-semibold text-[#b0b3c7] uppercase tracking-wider mb-3" x-show="sidebarOpen">Gestión</h3>
                <ul class="space-y-2">
                    <li class="flex items-center gap-3 text-[#b0b3c7] hover:text-white hover:bg-[#353a4a] rounded-lg px-3 py-2 cursor-pointer transition-all duration-200" onclick="window.location.href='#'">
                        <i class='bx bx-bar-chart-alt-2'></i>
                        <span x-show="sidebarOpen">Reportes</span>
                    </li>
                    <li class="flex items-center gap-3 text-[#b0b3c7] hover:text-white hover:bg-[#353a4a] rounded-lg px-3 py-2 cursor-pointer transition-all duration-200" onclick="window.location.href='#'">
                        <i class='bx bx-user-plus'></i>
                        <span x-show="sidebarOpen">Usuarios</span>
                    </li>
                </ul>
            </div>

            <!-- Sistema -->
            <div class="mb-6">
                <h3 class="text-xs font-semibold text-[#b0b3c7] uppercase tracking-wider mb-3" x-show="sidebarOpen">Sistema</h3>
                <ul class="space-y-2">
                    <li class="flex items-center gap-3 text-[#b0b3c7] hover:text-white hover:bg-[#353a4a] rounded-lg px-3 py-2 cursor-pointer transition-all duration-200">
                        <i class='bx bx-cog'></i>
                        <span x-show="sidebarOpen">Configuración</span>
                    </li>
                    <li class="flex items-center gap-3 text-[#b0b3c7] hover:text-white hover:bg-[#353a4a] rounded-lg px-3 py-2 cursor-pointer transition-all duration-200">
                        <i class='bx bx-help-circle'></i>
                        <span x-show="sidebarOpen">Ayuda</span>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Logout -->
        <div class="mt-auto pt-4 border-t border-[#353a4a]">
            <div class="flex items-center gap-3 text-[#b0b3c7] hover:text-red-400 cursor-pointer px-3 py-2 rounded-lg hover:bg-[#353a4a] transition-all duration-200">
                <i class='bx bx-log-out'></i>
                <span x-show="sidebarOpen">Cerrar Sesión</span>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main :class="sidebarOpen ? 'flex-1 p-10 fade-in ml-0' : 'flex-1 p-10 fade-in ml-0'" class="transition-all duration-300">
        <!-- Bienvenida -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-white mb-4">¡Bienvenido al Sistema de Facturación!</h1>
            <p class="text-[#b0b3c7] text-lg">Gestiona tu empresa de manera eficiente con nuestras herramientas integradas</p>
        </div>

            <!-- Tarjetas -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <div class="bg-[#232733] rounded-2xl shadow-lg p-6 card-hover cursor-pointer" onclick="window.location.href='#'">
                    <div class="flex items-center mb-4">
                        <div class="bg-blue-500 p-3 rounded-lg mr-4">
                            <i class='bx bx-buildings text-2xl text-white'></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Compañías</h3>
                            <p class="text-[#b0b3c7] text-sm">Gestionar empresas</p>
                        </div>
                    </div>
                    <p class="text-[#b0b3c7] mb-4">Administra la información de tus empresas, certificados y configuraciones.</p>
                    <div class="flex items-center text-blue-400">
                        <span class="text-sm font-semibold">Ir a Compañías</span>
                        <i class='bx bx-right-arrow-alt ml-2'></i>
                    </div>
                </div>

                <div class="bg-[#232733] rounded-2xl shadow-lg p-6 card-hover cursor-pointer" onclick="window.location.href='#'">
                    <div class="flex items-center mb-4">
                        <div class="bg-green-500 p-3 rounded-lg mr-4">
                            <i class='bx bx-receipt text-2xl text-white'></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Facturas</h3>
                            <p class="text-[#b0b3c7] text-sm">Generar documentos</p>
                        </div>
                    </div>
                    <p class="text-[#b0b3c7] mb-4">Crea y envía facturas electrónicas de manera rápida y segura.</p>
                    <div class="flex items-center text-green-400">
                        <span class="text-sm font-semibold">Nueva Factura</span>
                        <i class='bx bx-right-arrow-alt ml-2'></i>
                    </div>
                </div>

                <div class="bg-[#232733] rounded-2xl shadow-lg p-6 card-hover cursor-pointer" onclick="window.location.href='#'">
                    <div class="flex items-center mb-4">
                        <div class="bg-purple-500 p-3 rounded-lg mr-4">
                            <i class='bx bx-bar-chart-alt-2 text-2xl text-white'></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Reportes</h3>
                            <p class="text-[#b0b3c7] text-sm">Análisis y estadísticas</p>
                        </div>
                    </div>
                    <p class="text-[#b0b3c7] mb-4">Visualiza reportes detallados y estadísticas del sistema.</p>
                    <div class="flex items-center text-purple-400">
                        <span class="text-sm font-semibold">Ver Reportes</span>
                        <i class='bx bx-right-arrow-alt ml-2'></i>
                    </div>
                </div>

                <div class="bg-[#232733] rounded-2xl shadow-lg p-6 card-hover cursor-pointer" onclick="window.location.href='#'">
                    <div class="flex items-center mb-4">
                        <div class="bg-orange-500 p-3 rounded-lg mr-4">
                            <i class='bx bx-user-plus text-2xl text-white'></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Usuarios</h3>
                            <p class="text-[#b0b3c7] text-sm">Gestión de accesos</p>
                        </div>
                    </div>
                    <p class="text-[#b0b3c7] mb-4">Crea y administra usuarios del sistema con diferentes permisos.</p>
                    <div class="flex items-center text-orange-400">
                        <span class="text-sm font-semibold">Crear Usuario</span>
                        <i class='bx bx-right-arrow-alt ml-2'></i>
                    </div>
                </div>
            </div>

            <!-- Estadísticas -->
            <div class="bg-[#232733] rounded-2xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-white mb-6">Resumen del Sistema</h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="text-center">
                        <div class="bg-blue-500 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class='bx bx-buildings text-2xl text-white'></i>
                        </div>
                        <h3 class="text-2xl font-bold text-white">{{ $companies ?? '0' }}</h3>
                        <p class="text-[#b0b3c7]">Empresas Registradas</p>
                    </div>
                    <div class="text-center">
                        <div class="bg-green-500 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class='bx bx-receipt text-2xl text-white'></i>
                        </div>
                        <h3 class="text-2xl font-bold text-white">0</h3>
                        <p class="text-[#b0b3c7]">Facturas Emitidas</p>
                    </div>
                    <div class="text-center">
                        <div class="bg-purple-500 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class='bx bx-user text-2xl text-white'></i>
                        </div>
                        <h3 class="text-2xl font-bold text-white">1</h3>
                        <p class="text-[#b0b3c7]">Usuarios Activos</p>
                    </div>
                    <div class="text-center">
                        <div class="bg-orange-500 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class='bx bx-check-circle text-2xl text-white'></i>
                        </div>
                        <h3 class="text-2xl font-bold text-white">100%</h3>
                        <p class="text-[#b0b3c7]">Sistema Operativo</p>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Estilos extra -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        .fade-in {
            animation: fadeIn 1s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }
    </style>
