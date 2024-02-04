<!DOCTYPE html>
<html lang="ru" style="margin: 0; padding: 0">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <title>Архив банов</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
    <link href="{{ asset('asset/styles.css')  }}" rel="stylesheet">
    <link href="{{ asset('asset/media.css')  }}" rel="stylesheet">
    <link href="{{ asset('asset/header/style-header.css')  }}" rel="stylesheet">
    <link href="{{ asset('asset/account/style-account.css') }}" rel="stylesheet">
    <link href="{{ asset('asset/search/style-search.css') }}" rel="stylesheet">
    <link href="{{ asset('asset/popup/style-popup.css') }}" rel="stylesheet">
    <link href="{{ asset('asset/loading/style-loading.css') }}" rel="stylesheet">
</head>
<body>
@include('includes/loading/loading')
@include('includes/popup/popup')
@include('includes/header/header')
@include('includes/account/auth')
<div class="body_container">
    @include('includes/account/account')
    @include('includes/search/search')
</div>

</body>
