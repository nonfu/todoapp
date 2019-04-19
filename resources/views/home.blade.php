@extends('layouts.app')

@section('content')
    <div class="container px-4 sm:px-0 mx-auto py-8">
        <tasks-component :initial-tasks="{{ $tasks }}"></tasks-component>
    </div>
@endsection
