<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="{{ asset('favicon.png') }}">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/web-app.css') }}" rel="stylesheet">
</head>
<body>
<div id="app">
    <nav class="navbar navbar-default navbar-static-top">
        <div class="container">
            <div class="navbar-header">

                <!-- Collapsed Hamburger -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                        data-target="#app-navbar-collapse" aria-expanded="false">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <!-- Branding Image -->
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <!-- Left Side Of Navbar -->
                <ul class="nav navbar-nav">
                    &nbsp;
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    <!-- Authentication Links -->
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"
                           aria-haspopup="true" v-pre>
                            {{ $user->name }} <span class="caret"></span>
                        </a>

                        <ul class="dropdown-menu">
                            <li>
                                <a href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                    General Logout
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                      style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Dashboard</div>

                    <div class="panel-body">
                        <form class="form-horizontal" method="POST" action="{{ route('logout') }}">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <div class="col-md-8 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary">
                                        General Logout
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="panel-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

                        {{--You are logged in!--}}
                        @if ($user->type == 'teacher')
                            <div class="row">
                                <h3>User Profile</h3>
                                <p>User Type: Teacher</p>
                                <p>Name: {{ $user->name }}</p>
                                <p>Email: {{ $user->email }}</p>
                            </div>

                            <div class="row">
                                <h3>My Schools</h3>
                                @if ($schools->isNotEmpty())
                                    @foreach ($schools as $school)
                                        <p>School: {{ $school->name }}</p>
                                    @endforeach
                                @else
                                    <p>There is No School Yet.</p>
                                @endif
                            </div>

                            <div class="row">
                                <h3>My Students</h3>
                                @if (count($students) > 0)
                                    @foreach ($students as $student)
                                        <p>
                                            Student: {{ $student['name'] }}
                                            &nbsp;&nbsp;&nbsp;&nbsp;
                                            <a target="_blank" rel="noopener noreferrer"
                                               href="https://icademi-chat.herokuapp.com?ws_token={{ $user->ws_token }}&current_contact_tag=student-{{ $student['id'] }}">
                                                Chat with this student
                                            </a>
                                        </p>
                                    @endforeach
                                @else
                                    <p>There is No Student Yet.</p>
                                @endif
                            </div>
                        @else
                            <div class="row">
                                <h3>User Profile</h3>
                                <p>User type: Student</p>
                                <p>Name: {{ $user->name }}</p>
                                <p>Email: {{ $user->email }}</p>
                            </div>

                            <div class="row">
                                <h3>My School</h3>
                                <p>School: {{ $school->name }}</p>
                            </div>

                            <div class="row">
                                <h3>My Teachers</h3>
                                @foreach ($teachers as $teacher)
                                    <p>
                                        Teacher: {{ $teacher['name'] }}
                                        &nbsp;&nbsp;&nbsp;&nbsp;
                                        <a href="javascript:void(0)" class="teacher-is-followed"
                                           data-is-followed="{{ $teacher['is_followed'] ? 'yes' : 'no' }}"
                                           data-teacher-id="{{ $teacher['id'] }}">
                                            {{ $teacher['is_followed'] ? 'Unfollow this teacher' : 'Follow this teacher' }}
                                        </a>
                                        &nbsp;&nbsp;&nbsp;&nbsp;
                                        <a target="_blank" rel="noopener noreferrer"
                                           href="https://icademi-chat.herokuapp.com?ws_token={{ $user->ws_token }}&current_contact_tag=teacher-{{ $teacher['id'] }}">
                                            Chat with this teacher
                                        </a>
                                    </p>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div style="display: none;" data-token="{{ $token }}" id="token"></div>
</div>

<!-- Scripts -->
<script src="{{ asset('js/web-app.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.24.0/axios.js"
        integrity="sha512-RT3IJsuoHZ2waemM8ccCUlPNdUuOn8dJCH46N3H2uZoY7swMn1Yn7s56SsE2UBMpjpndeZ91hm87TP1oU6ANjQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    (function () {
        const teachers = document.getElementsByClassName('teacher-is-followed');
        const token = document.getElementById('token').getAttribute('data-token');
        const axiosInstance = axios.create({
            baseURL: 'https://icademi.herokuapp.com/api/',
            timeout: 9000,
            headers: {
                Accept: "application/json",
                Authorization: 'Bearer ' + token,
            },
        });
        for (let teacher of teachers) {
            // console.log(teacher);
            let teacher_id = teacher.getAttribute('data-teacher-id');
            let is_followed = teacher.getAttribute('data-is-followed');
            teacher.onclick = function () {
                if (is_followed === 'yes') {
                    axiosInstance
                        .get("student/unfollow_teacher?teacher_id=" + teacher_id, {
                            // teacher_id: teacher_id,
                        })
                        .then(function (response) {
                            console.log(response);
                            teacher.setAttribute('data-is-followed', 'no');
                            // teacher.innerText = 'Follow this teacher';
                            teacher.textContent = 'Follow this teacher';
                        })
                        .catch(function (error) {
                            console.log(error);
                        });
                } else {
                    axiosInstance
                        .get("student/follow_teacher?teacher_id=" + teacher_id, {
                            // teacher_id: teacher_id,
                        })
                        .then(function (response) {
                            console.log(response);
                            teacher.setAttribute('data-is-followed', 'yes');
                            // teacher.innerText = 'Unfollow this teacher';
                            teacher.textContent = 'Unfollow this teacher';
                        })
                        .catch(function (error) {
                            console.log(error);
                        });
                }
            }
        }
    })();
</script>
</body>
</html>