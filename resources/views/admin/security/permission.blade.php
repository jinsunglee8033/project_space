@extends('layouts.dashboard')

@section('title', 'Access Denied')

@section('content')
    <div class="container text-center" style="margin-top: 10%;">
        <h1 class="display-4">🚫 Access Denied</h1>
        <p class="lead mt-3">You do not have permission to view this page.</p>
        <p>If you believe this is an error, please contact the administrator.</p>

        <a href="{{ url('/admin/dashboard') }}" class="btn btn-primary mt-4">Return to Home</a>
    </div>
@endsection