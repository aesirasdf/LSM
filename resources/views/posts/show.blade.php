@extends('layouts.app')

@section('content')
    <div class="jumbotron">
        <h1 class="display-4">{{ $Post->User->name }}</h1>
        <p class="lead">{{ $Post->content }}</p>
    </div>
@endsection