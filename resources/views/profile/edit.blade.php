@extends('layouts.app')

@section('content')

<div class="page-header">
    {{ __('Profile') }}
</div>

<div class="mb-5">
    <div class="card">
        @include('profile.partials.update-profile-information-form')
    </div>
</div>

<div class="mb-5">
    <div class="card">
        @include('profile.partials.update-password-form')
    </div>
</div>

<div class="mb-5">
    <div class="card">
        @include('profile.partials.delete-user-form')
    </div>
</div>

@endsection
