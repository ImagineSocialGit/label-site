<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    @if(isset($metaData))
        <meta property="title" content="{{ $metaData->title }}">
        <meta property="description" content="{{ $metaData->description }}">
        <meta property="image" content="{{ $metaData->image }}">

        <meta property="og:title" content="{{ $metaData->title }}">
        <meta property="og:description" content="{{ $metaData->description }}">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ config('app.url') }}">
        <meta property="og:image" content="{{ $metaData->image }}">
    @endif

    @if(isset($favicon))
        <link rel="icon" type="image/png" href="/storage/favicon/favicon-96x96.png" sizes="96x96" />
        <link rel="icon" type="image/svg+xml" href="/storage/favicon/favicon.svg" />
        <link rel="shortcut icon" href="/storage/favicon/favicon.ico" />
        <link rel="apple-touch-icon" sizes="180x180" href="/storage/favicon/apple-touch-icon.png" />
        <link rel="manifest" href="/storage/favicon/site.webmanifest" />
    @endif

    <script>document.cookie = "name=oeschger; SameSite=None; Secure";</script>
    <script defer src="{{config('services.alpine.intersect')}}"></script>
    <script defer src="{{config('services.alpine.mask')}}"></script>
    <script defer src="{{config('services.alpine.standard')}}"></script>

    <title>{{ config('app.site-name')}} | {{$title}}</title>
    <style>
        html {
            scroll-behavior: smooth;
        },
        [x-cloak] { display: none !important; }
    </style>
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
</head>