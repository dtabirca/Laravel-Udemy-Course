@extends('layouts.app')

@section('title', 'Contact page')

@section('content')
    <h1>{{ __('Contact') }}</h1> 
    <p>{{ __('Hello this is contact!') }}</p>  

    @can('home.secret')
        <p>
            <a href="{{ route('home.secret') }}">Special contact details</a>
        </p>
    @endcan
@endsection