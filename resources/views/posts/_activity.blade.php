<div class="container">

    <x-card :title="__('Most Commented')" :subtitle="__('What people are currently talking about.')">
        @slot('items')
            @foreach ($mostCommented as $post)
            <li class="list-group-item">
                <a href="{{ route('posts.show', ['post' => $post->id]) }}">
                    {{ $post->title }}
                </a>
            </li>
            @endforeach
        @endslot
    </x-card>

    <x-card :title="__('Most Active')" :subtitle="__('Writters with the most posts written.')">
        @slot('items', collect($mostActive)->pluck('name'))
    </x-card>
      
    <x-card :title="__('Most Active Last Month')" :subtitle="__('Writters with the most posts written.')">
        @slot('items', collect($mostActiveLastMonth)->pluck('name'))
    </x-card>

</div>