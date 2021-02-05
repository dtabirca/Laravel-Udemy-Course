@extends('layouts.app')

@section('title', $post->title)

@section('content')
    {{-- @if($post['is_new'])
    <div>A new post! using if</div>
    @else
    <div>Post is old! using else</div>
    @endif

    @unless ($post['is_new'])
    <div>It is an old post... using unless</div>
    @endunless --}}
    <h1>{{ $post->title }}</h1>
    <p>{{ $post->content }}</p>
    <p>Added {{ $post->created_at->diffForHumans() }}</p>

    @if (now()->diffInMinutes($post->created_at) < 5)
        <div class="alert alert-info">New!</div>
    @endif
    {{-- @isset($post['has_comments'])
    <div>The post has some comments... using isset</div>
    @endisset --}}

    <h4>Comments</h4>
    @forelse ($post->comments as $comment)
        <p>
            {{ $comment->content }}
        </p>
        <p class="text-muted">
            , added {{ $comment->created_at->diffForHumans() }}
        </p>
    @empty
        <p>No comments yet.</p>
    @endforelse
@endsection