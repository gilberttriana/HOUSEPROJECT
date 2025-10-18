<!DOCTYPE html>
<html class="dark" lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>@yield('title', 'HouseBuild - Maestro de Obras')</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: "#F4C753",
                        "background-dark": "#221d10",
                        "card-dark": "#1F2937",
                        "border-dark": "#374151",
                        "text-dark": "#F9FAFB",
                        "text-secondary-dark": "#9CA3AF",
                        "background-light": "#f8f7f6"
                    },
                    fontFamily: {
                        display: "Inter"
                    },
                    borderRadius: {
                        DEFAULT: "0.25rem",
                        lg: "0.5rem",
                        xl: "0.75rem",
                        full: "9999px"
                    }
                }
            }
        };
    </script>
    @stack('styles')
</head>
<body class="bg-background-dark font-display text-text-dark">
    <div class="flex h-screen">
        <!-- Incluir Sidebar como Componente -->
        @include('components.sidebar-maestro')

        <!-- Contenido Principal -->
        <main class="flex-1 p-8 overflow-y-auto">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
