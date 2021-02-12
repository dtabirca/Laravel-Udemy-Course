<?php

namespace App\Mail;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class CommentPosted extends Mailable
{
    use Queueable, SerializesModels;

    public $comment;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = "A comment was posted on your {$this->comment->commentable->title} blog post.";
        return $this->subject($subject)
            // ->attach(
            //     storage_path('app/public') . '/' . $this->comment->user->image->path,
            //     [
            //         'as' => 'prifile_picture.jpeg',
            //         'mime' => 'image/jpeg'
            //     ]
            // )
            //->attachFromStorage($this->comment->user->image->path, 'profile_picture.jpeg')
            //->attachFromStorage('public', $this->comment->user->image->path)
            // ->attachData(Storage::get($this->comment->user->image->path), 'profile_picture.jpeg', [
            //     'mime' => 'image/jpeg'
            // ])
            ->from('admin@laravel.ro', 'Admin')
            ->view('emails.posts.commented');
    }
}
