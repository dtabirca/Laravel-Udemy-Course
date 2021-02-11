@extends('layouts.app')

@section('title', $post->title)

@section('content')
<div class="row">
    <div class="col-8">
    {{-- @if($post['is_new'])
    <div>A new post! using if</div>
    @else
    <div>Post is old! using else</div>
    @endif

    @unless ($post['is_new'])
    <div>It is an old post... using unless</div>
    @endunless --}}

        @if ($post->image)
            <div style="background-image: url('{{ $post->image->url() }}'); min-height: 500px; color:white; text-align: center; background-attachment: fixed;">
                <h1 style="padding-top: 100px; text-shadow: 1px 2px #000">
        @else
            <h1>
        @endif

            {{ $post->title }}
            <x-badge :show="now()->diffInMinutes($post->created_at) < 60">
                Brand new Post!
            </x-badge>

        @if ($post->image)
            </h1></div>
        @else
            </h1>
        @endif
        
        <p>{{ $post->content }}</p>

        <x-updated :date="$post->created_at" :name="$post->user->name" :userId="$post->user->id">
        </x-updated>

        <x-updated :date="$post->updated_at" :name="$post->user->name" :userId="$post->user->id">
            Updated
        </x-updated>

        <x-tags :tags="$post->tags">
        </x-tags>
            
        <p>
            Currently read by {{ $counter }} people.
        </p>

        {{-- @isset($post['has_comments'])
        <div>The post has some comments... using isset</div>
        @endisset --}}

        <h4>Comments</h4>

        <x-comment-form :route="route('posts.comments.store', ['post' => $post->id])">
        </x-comment-form>

        <x-comment-list :comments="$post->comments">
        </x-comment-list>

    </div>
    <div class="col-4">
        @include('posts._activity') 
    </div>
</div>   
@endsection