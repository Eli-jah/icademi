<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="{{ asset('favicon.png') }}">

    <title>Laravel</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <style>
        html, body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Raleway', sans-serif;
            font-weight: 100;
            /*height: 100vh;*/
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        .content {
            text-align: center;
        }

        .title {
            font-size: 84px;
        }

        .links > a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }

        .m-b-md {
            margin-bottom: 30px;
        }
    </style>

    <!-- Socket.io Client -->
    {{-- <script src="https://cdn.socket.io/4.3.2/socket.io.min.js" integrity="sha384-KAZ4DtjNhLChOB/hxXuKqhMLYvx3b5MlT55xPEiNmREKRzeEm+RVPlTnAn0ajQNs" crossorigin="anonymous"></script>--}}
    {{-- <script src="{{ asset('socket.io.js') }}"></script>--}}

</head>
<body>
{{--<div class="flex-center position-ref full-height">--}}
<div class="flex-center position-ref">
    @if (Route::has('login'))
        <div class="top-right links">
            @auth
                <a href="{{ url('/home') }}">Home</a>
            @else
                <a href="{{ route('login') }}">Login</a>
                <a href="{{ route('register') }}">Register</a>
            @endauth
        </div>
    @endif

    <div class="content">
        {{-- <div class="title m-b-md">--}}
        {{-- Laravel--}}
        {{-- </div>--}}

        <h4>Welcome to Icademi!</h4>

        <h5>URLs:</h5>
        <div>
            <p>
                Web: <a href="https://icademi.herokuapp.com/">https://icademi.herokuapp.com/</a>
            </p>
            <p>
                Apidoc: <a href="https://icademi.herokuapp.com/apidoc/index.html">https://icademi.herokuapp.com/apidoc/index.html</a>
            </p>
            <p>
                Admin:(admin@admin) <a href="https://icademi.herokuapp.com/admin/">https://icademi.herokuapp.com/admin/</a>
            </p>
            <p>
                Websocket Chat: <a href="https://icademi-chat.herokuapp.com/">https://icademi-chat.herokuapp.com/</a>
            </p>
        </div>

        <h5>GitHub Repositories:</h5>
        <div>
            <p>
                icademi: <a href="https://github.com/Eli-jah/icademi">https://github.com/Eli-jah/icademi</a>
            </p>
            <p>
                icademi-chat: <a href="https://github.com/Eli-jah/icademi-chat">https://github.com/Eli-jah/icademi-chat</a>
            </p>
        </div>

        <h5>Teachers:</h5>
        <div>
            <p>
                <span>Account: teacher-email-1@test.com || Password: Qwerty123456</span>
            </p>
            <p>
                <span>Account: teacher-email-2@test.com || Password: Qwerty123456</span>
            </p>
            <p>
                <span>Account: teacher-email-3@test.com || Password: Qwerty123456</span>
            </p>
            <p>
                <span>Account: teacher-email-4@test.com || Password: Qwerty123456</span>
            </p>
            <p>
                <span>Account: teacher-email-5@test.com || Password: Qwerty123456</span>
            </p>
            <p>
                <span>Account: teacher-email-6@test.com || Password: Qwerty123456</span>
            </p>
        </div>

        <h5>Students:</h5>
        <div>
            <p>
                <span>Account: student-email-1@test.com || Password: Qwerty123456</span>
            </p>
            <p>
                <span>Account: student-email-2@test.com || Password: Qwerty123456</span>
            </p>
            <p>
                <span>Account: student-email-3@test.com || Password: Qwerty123456</span>
            </p>
            <p>
                <span>Account: student-email-4@test.com || Password: Qwerty123456</span>
            </p>
            <p>
                <span>Account: student-email-5@test.com || Password: Qwerty123456</span>
            </p>
            <p>
                <span>Account: student-email-6@test.com || Password: Qwerty123456</span>
            </p>
        </div>

        <div class="links">
            <a href="https://laravel.com/docs">Documentation</a>
            <a href="https://laracasts.com">Laracasts</a>
            <a href="https://laravel-news.com">News</a>
            <a href="https://forge.laravel.com">Forge</a>
            <a href="https://github.com/laravel/laravel">GitHub</a>
        </div>
    </div>
</div>
</body>
</html>
