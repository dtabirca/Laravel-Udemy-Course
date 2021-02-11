@extends('layouts.app')

@section('title', 'Blog posts')

@section('content')
<div class="row">
    <div class="col-8">
        {{-- @each('posts.partials.post', $posts, 'post') --}}
        @forelse($posts as $key => $post)
            @include('posts.partials.post')
        @empty
            No blog posts yet!
        @endforelse
    </div>
    <div class="col-4">
        @include('posts._activity')  
    </div>
</div>
@endsection