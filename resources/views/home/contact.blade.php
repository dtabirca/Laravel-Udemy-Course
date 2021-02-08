@extends('layouts.app')

@section('title', 'Contact page')

@section('content')
    <h1>Contact Page</h1> 
    <p>This is the content of the contact page!</p>  

    @can('home.secret')
        <p>
            <a href="{{ route('home.secret') }}">Special contact details</a>
        </p>
    @endcan
@endsection