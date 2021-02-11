<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePost;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Comment;
use App\Models\Image;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class PostsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['create', 'edit', 'store', 'destroy', 'update']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // lazy loading example
        // DB::enableQueryLog();
        // $posts = BlogPost::all();
        // foreach ($posts as $post) {
        //     $comments = Comment::where('blog_post_id', '=', $post->id)->get();
        //     foreach ($comments as $comment) {
        //         echo $comment->content;
        //     }
        // }
        // dd(DB::getQueryLog());

        // $posts = Cache::remember('postsIndex', now()->addSeconds(100), function() {
        //     return BlogPost::latest()->withCount('comments')->get();
        // });

        // $mostCommented = Cache::remember('blog-post-commented', now()->addSeconds(60), function() {
        //     return BlogPost::mostCommented()->take(5)->get();
        // });

        // $mostActive = Cache::remember('users-most-active', now()->addSeconds(60), function() {
        //     return User::withMostBlogPosts()->take(5)->get();
        // });

        // $mostActiveLastMonth = Cache::remember('users-most-active-last-month', now()->addSeconds(60), function() {
        //     return User::withMostBlogPostsLastMonth()->take(5)->get();
        // });

        // try{
            //     $redis=Redis::connect('127.0.0.1',6379);
            //     $allKeys = Redis::keys('*');
            //     return response('redis working');
            // }catch(\Predis\Connection\ConnectionException $e){
            //     return response('error connection redis');
            // }

            //dd(Redis::keys('mostCommented'));
            //dd($mostCommented);

            // comments_count
        return view(
            'posts.index',
            [
                'posts' => BlogPost::latestWithRelations()->get(),
                // 'mostCommented' => $mostCommented,
                // 'mostActive' => $mostActive,
                // 'mostActiveLastMonth' => $mostActiveLastMonth,
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //$this->authorize('posts.create');
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePost $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = $request->user()->id;
        $post = BlogPost::create($validated);

        // $post = new BlogPost();
        // $post->title = $validated['title'];
        // $post->content = $validated['content'];
        // //$post->fill();
        // $post->save();

        // $post2 = BlogPost::create();
        // // or
        // $post2 = BlogPost::make();
        // $post2->save();

        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('thumbnails');
            $post->image()->save(
                Image::make(['path' => $path])
                //Image::create(['path' => $path])
            );
            // dump($file);
            // dump($file->getClientMimeType());
            // dump($file->getClientOriginalExtension());

            //$file->store('thumbnails');
            //Storage::disk('public')->putFile('thumbnails', $file);

            //$name1 = $file->storeAs('thumbnails', $post->id . '.' . $file->guessExtension());
            //$name2 = Storage::disk('local')->putFileAs('thumbnails', $file, $post->id . '.' . $file->guessExtension());
            
            //Storage::url($name1);
            //Storage::disk('local')->url($name2);
        }

        $request->session()->flash('status', 'The blog post was created');

        return redirect()->route('posts.show', ['post' => $post->id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // abord_if(!isset($this->posts[$id]), 404);
        // return view('posts.show', [
        //     'post' => BlogPost::with(['comments' => function ($query) {
        //         return $query->latest();
        //     }])->findOrFail($id),
        // ]);

        $blogPost = Cache::tags(['blog-post'])->remember("blog-post-{$id}", now()->addSeconds(60), function() use($id) {
            return BlogPost::with('comments', 'tags', 'user', 'comments.user')
                ->findOrFail($id);
        });

        $sessionId = session()->getId();
        $counterKey = "blog-post-{$id}-counter";
        $usersKey = "blog-post-{$id}-users";

        $users = Cache::tags('blog-post')->get($usersKey, []);
        $usersUpdate = [];
        $difference = 0;
        $now = now();
        //dd($users);
        foreach ($users as $session => $lastVisit) {
            if ($now->diffInMinutes($lastVisit) >= 1) {
                $difference--;
            } else {
                $usersUpdate[$session] = $lastVisit;
            }
        }
            
        if (
            !array_key_exists($sessionId, $users)
            || $now->diffInMinutes($users[$sessionId]) >= 1
        ) {
            $difference++;
        }

        $usersUpdate[$sessionId] = $now;
        Cache::tags('blog-post')->forever($usersKey, $usersUpdate);

        if (!Cache::tags('blog-post')->has($counterKey)) {
            Cache::tags('blog-post')->forever($counterKey, 1);
        } else {
            Cache::tags('blog-post')->increment($counterKey, $difference);
        }
        
        $counter = Cache::tags('blog-post')->get($counterKey);

        return view('posts.show', [
            'post' => $blogPost,
            'counter' => $counter,
        ]);        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $post = BlogPost::findOrFail($id);

        // if (Gate::denies('update-post', $post)) {
        //     abort(403, "You can't edit this post!");
        // }  
        
        $this->authorize($post);
        
        return view('posts.edit', ['post' => $post]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StorePost $request, $id)
    {
        $post = BlogPost::findOrFail($id);

        // if (Gate::denies('update-post', $post)) {
        //     abort(403, "You can't edit this post!");
        // }

        $this->authorize($post);

        $validated = $request->validated();
        $post->fill($validated);

        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('thumbnails');
            if ($post->image) {
                Storage::delete($post->image->path);
                $post->image->path = $path;
                $post->image->save();
            } else {
                $post->image()->save(
                    Image::make(['path' => $path])
                    //Image::create(['path' => $path])
                );
            }
        } 
        $post->save();

        $request->session()->flash('status', 'Blog post was updated!');

        return redirect()->route('posts.show', ['post' => $post->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = BlogPost::findOrFail($id);

        // if (Gate::denies('delete-post', $post)) {
        //     abort(403, "You can't delete this post!");
        // }

        $this->authorize($post);

        $post->delete();

        session()->flash('status', 'Blog post was deleted!');

        return redirect()->route('posts.index');
    }
}
