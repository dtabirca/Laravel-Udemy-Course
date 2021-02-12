@forelse ($comments as $comment)
<p>
    {{ $comment->content }}
</p>

<x-tags :tags="$comment->tags">
</x-tags>

<x-updated :date="$comment->created_at" :name="$comment->user->name" :userId="$comment->user->id">
</x-updated>        
@empty
<p>{{ trans_choice('comments', 0) }}</p>
@endforelse