<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreComment;
use App\Http\Resources\Comment;
use App\Jobs\NotifyUsersPostWasCommented;
use App\Jobs\ThrottledMail;
use App\Mail\CommentPosted;
use App\Mail\CommentPostedMarkdown;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Http\Resources\Comment as CommentResource;

class PostCommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['store']);
    }

    public function index(BlogPost $post)
    {
        //dump(get_class($post->comments));
        //return new CommentResource($post->comments->first());
        return CommentResource::collection($post->comments()->with('user')->get());
        //return $post->comments()->with('user')->get();
    }

    /**
     * 
     */
    public function store(BlogPost $post, StoreComment $request)
    {
        // Comment::create()
        $comment = $post->comments()->create([
            'content' => $request->input('content'),
            'user_id' => $request->user()->id,
        ]);

        // Mail::to($post->user)->send(
        //     new CommentPostedMarkdown($comment)
        // );

        // Mail::to($post->user)->queue(
        //     new CommentPostedMarkdown($comment)
        // );

        event(new CommentPosted($comment));

        // ThrottledMail::dispatch(new CommentPostedMarkdown($comment), $post->user)
        //     ->onQueue('low');

        // NotifyUsersPostWasCommented::dispatch($comment)
        //     ->onQueue('high');

        // $when = now()->addMinutes(1);

        // Mail::to($post->user)->later(
        //     $when,
        //     new CommentPostedMarkdown($comment)
        // );

        return redirect()->back()
            ->withStatus('Comment was created!');
    }
}
