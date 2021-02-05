@extends('layouts.app')

@section('title', 'Blog posts')

@section('content')
{{-- @each('posts.partials.post', $posts, 'post') --}}
@forelse($posts as $key => $post)
    @include('posts.partials.post')
@empty
    No blog posts yet!
@endforelse
@endsection