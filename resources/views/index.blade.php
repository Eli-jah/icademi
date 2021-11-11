<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="{{ asset('css/web-app.css') }}" rel="stylesheet" type="text/css"/>

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.png') }}">

    <title>ICADEMI CHAT ONLINE</title>

    <script type='text/javascript'>
        window.Laravel = <?php echo json_encode(['csrfToken' => csrf_token()]); ?>
    </script>
</head>
<body>
<div id="app"></div>
<script type="text/javascript" src="{{ asset('js/web-app.js') }}"></script>
</body>
</html>