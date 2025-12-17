<!DOCTYPE html>
<html class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Client Portal & Project Tracker</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.19/index.global.min.css' rel='stylesheet' />
</head>
<body class="bg-body text-gray-900 h-full w-full">
    <div id="app" class="h-full"></div>
</body>
</html>
