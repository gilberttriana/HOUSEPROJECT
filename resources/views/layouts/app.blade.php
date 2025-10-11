<!DOCTYPE html>
<html lang="en" class="dark">
<head>
  <meta charset="utf-8"/>
  <title>Admin Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Fonts & Icons -->
  <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?display=swap&family=Inter:wght@400;500;700;900&family=Noto+Sans:wght@400;500;700;900">
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <script id="tailwind-config">
    tailwind.config = {
      darkMode: "class",
      theme: {
        extend: {
          colors: {
            primary: "#1173d4",
            "background-light": "#101922",
            "background-dark": "#101922",
          },
          fontFamily: {
            display: ["Inter", "Noto Sans"],
          },
          borderRadius: {
            DEFAULT: "0.25rem", 
            lg: "0.5rem", 
            xl: "0.75rem", 
            full: "9999px"
          },
        },
      },
    }
  </script>
</head>
<body class="bg-background-light dark:bg-background-dark font-display">
  <div class="flex min-h-screen">
    <x-sidebar />
    <main class="flex-1 p-8 lg:p-10 bg-gray-50 dark:bg-gray-900/50">
      @yield('content')
    </main>
  </div>
  @yield('scripts')
</body>
</html>