<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>🌱 AgriManager - @yield('title', 'Tableau de Bord')</title>
    
    <!-- TAILWIND CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'agri-green': {
                            50: '#f0f9f0',
                            100: '#dbf0db',
                            500: '#4CAF50',
                            600: '#388E3C',
                            700: '#1B5E20',
                            800: '#0d4211',
                        },
                        'agri-orange': '#FF9800',
                        'agri-blue': '#2196F3',
                        'agri-red': '#f44336',
                        'agri-yellow': '#FFC107',
                    }
                }
            }
        }
    </script>
    
    <style>
        /* Styles personnalisés additionnels */
        .sidebar {
            background: linear-gradient(180deg, #1B5E20 0%, #0d4211 100%);
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            width: 16rem; /* 64 * 0.25rem = 16rem */
            overflow-y: auto;
            z-index: 40;
        }
        
        .main-content {
            margin-left: 16rem; /* Même largeur que le sidebar */
            width: calc(100% - 16rem);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .nav-link {
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }
        
        .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            border-left-color: #4CAF50;
        }
        
        .nav-link.active {
            background: rgba(255, 255, 255, 0.15);
            border-left-color: #4CAF50;
        }
        
        .stat-card {
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #4CAF50 0%, #1B5E20 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(76, 175, 80, 0.4);
        }

        /* Personnalisation de la scrollbar */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        
        .sidebar::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.1);
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
        }
        
        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-50 font-sans">
    <div class="flex">
        <!-- Sidebar Fixe -->
        <div class="sidebar text-white flex flex-col">
            <!-- Logo -->
            <div class="p-6 border-b border-green-700">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center">
                        <i class="fas fa-tractor text-xl"></i>
                    </div>
                    <div>
                        <h1 ><strong>🌱 AgriManager </strong></h1>
                        <p class="text-green-200 text-sm">Gestion Agricole</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 p-4 space-y-1">
                <a href="{{ route('dashboard') }}" 
                   class="nav-link flex items-center space-x-3 p-3 rounded-lg {{ request()->is('/') ? 'active' : '' }}">
                    <i class="fas fa-chart-line w-5"></i>
                    <span>Tableau de Bord</span>
                </a>

                <a href="{{ route('recoltes.index') }}" 
                   class="nav-link flex items-center space-x-3 p-3 rounded-lg {{ request()->is('recoltes*') ? 'active' : '' }}">
                    <i class="fas fa-seedling w-5"></i>
                    <span>Récoltes</span>
                </a>

                <a href="{{ route('ventes.index') }}" 
                   class="nav-link flex items-center space-x-3 p-3 rounded-lg {{ request()->is('ventes*') ? 'active' : '' }}">
                    <i class="fas fa-shopping-cart w-5"></i>
                    <span>Ventes</span>
                </a>

                <a href="{{ route('stocks.index') }}" 
                   class="nav-link flex items-center space-x-3 p-3 rounded-lg {{ request()->is('stocks*') ? 'active' : '' }}">
                    <i class="fas fa-boxes w-5"></i>
                    <span>Stocks</span>
                </a>

                <a href="{{ route('produits.index') }}" 
                   class="nav-link flex items-center space-x-3 p-3 rounded-lg {{ request()->is('produits*') ? 'active' : '' }}">
                    <i class="fas fa-apple-alt w-5"></i>
                    <span>Produits</span>
                </a>

                <a href="{{ route('varietes.index') }}" 
                   class="nav-link flex items-center space-x-3 p-3 rounded-lg {{ request()->is('varietes*') ? 'active' : '' }}">
                    <i class="fas fa-leaf w-5"></i>
                    <span>Variétés</span>
                </a>
            </nav>

            <!-- User Info -->
            <div class="p-4 border-t border-green-700">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <p class="font-medium">Administrateur</p>
                        <p class="text-xs text-green-200">Connecté</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <header class="bg-white border-b shadow-sm px-6 py-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800">
                            <i class="fas @yield('icon', 'fa-chart-line') text-green-500 mr-2"></i>
                            @yield('title', 'Tableau de Bord')
                        </h2>
                        <p class="text-sm text-gray-600">@yield('subtitle', 'Vue d\'ensemble de votre exploitation')</p>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <div class="text-right">
                            <p class="text-sm text-gray-600">{{ now()->format('l') }}</p>
                            <p class="font-medium text-gray-800">{{ now()->format('d/m/Y') }}</p>
                        </div>
                        
                        <button class="relative p-2 text-gray-600 hover:text-green-500">
                            <i class="fas fa-bell text-lg"></i>
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <main class="flex-1 overflow-y-auto p-6 bg-amber-50">
                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-lg border border-green-300 flex items-center">
                        <i class="fas fa-check-circle mr-3 text-green-500"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-lg border border-red-300 flex items-center">
                        <i class="fas fa-exclamation-circle mr-3 text-red-500"></i>
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t px-6 py-3">
                <div class="flex justify-between items-center text-sm text-gray-600">
                    <div>
                        <span>© {{ date('Y') }} AgriManager - Système de Gestion Agricole</span>
                    </div>
                    <div>
                        <span>Dernière mise à jour: {{ now()->format('H:i') }}</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script>
        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            document.querySelectorAll('.alert-auto-hide').forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'agri': {
                            'green': {
                                50: '#f0f9f0',
                                100: '#dbf0db',
                                200: '#b9e1b9',
                                300: '#8bcc8b',
                                400: '#56b256',
                                500: '#4CAF50',
                                600: '#388E3C',
                                700: '#1B5E20',
                                800: '#0d4211',
                                900: '#052908',
                            },
                            'blue': {
                                500: '#2196F3',
                                600: '#1E88E5',
                            },
                            'orange': {
                                500: '#FF9800',
                                600: '#FB8C00',
                            },
                            'red': {
                                500: '#f44336',
                                600: '#e53935',
                            },
                            'yellow': {
                                500: '#FFC107',
                                600: '#FFB300',
                            }
                        }
                    },
                    animation: {
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'bounce-slow': 'bounce 2s infinite',
                        'float': 'float 6s ease-in-out infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-10px)' },
                        }
                    }
                }
            }
        }
    </script>

    @stack('scripts')
</body>
</html>