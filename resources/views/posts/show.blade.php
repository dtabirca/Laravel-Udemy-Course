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
    <h1>
        {{ $post->title }}
        <x-badge :show="now()->diffInMinutes($post->created_at) < 60">
            Brand new Post!
        </x-badge>
    </h1>
    
    <p>{{ $post->content }}</p>

    <x-updated :date="$post->created_at" :name="$post->user->name">
    </x-updated>

    <x-updated :date="$post->updated_at" :name="$post->user->name">
        Updated
    </x-updated>

    {{-- @isset($post['has_comments'])
    <div>The post has some comments... using isset</div>
    @endisset --}}

    <h4>Comments</h4>
    @forelse ($post->comments as $comment)
        <p>
            {{ $comment->content }}
        </p>
        <x-updated :date="$comment->created_at">
        </x-updated>        
    @empty
        <p>No comments yet.</p>
    @endforelse
@endsection