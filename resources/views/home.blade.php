@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Dashboard</div>

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
                                <h3>Schools</h3>
                                @foreach ($schools as $school)
                                    <p>School: {{ $school->name }}</p>
                                @endforeach
                            </div>

                            <div class="row">
                                <h3>Students</h3>
                                @foreach ($students as $student)
                                    <p>Student: {{ $student->name }}</p>
                                    <a target="_blank" rel="noopener noreferrer"
                                       href="https://icademi-chat.herokuapp.com?ws_token={{ $user->ws_token }}&current_contact_tag=student-{{ $student->id }}">
                                        Chat
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="row">
                                <h3>User Profile</h3>
                                <p>User type: Student</p>
                                <p>Name: {{ $user->name }}</p>
                                <p>Email: {{ $user->email }}</p>
                            </div>

                            <div class="row">
                                <h3>Schools</h3>
                                <p>School: {{ $school->name }}</p>
                            </div>

                            <div class="row">
                                <h3>Teachers</h3>
                                @foreach ($teachers as $teacher)
                                    <p id="teacher-{{ $teacher->id }}">Teacher: {{ $teacher->name }}
                                        <a href="javascript:void(0)" class="is-followed" data-teacher-id="{{ $teacher->id }}">
                                            {{ $teacher->is_followed ? 'Unfollow' : 'Follow' }}
                                        </a>
                                        <a target="_blank" rel="noopener noreferrer"
                                           href="https://icademi-chat.herokuapp.com?ws_token={{ $user->ws_token }}&current_contact_tag=teacher-{{ $teacher->id }}">
                                            Chat
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
    <div style="display: none;" data-token="{{ $token }}"></div>
@endsection
