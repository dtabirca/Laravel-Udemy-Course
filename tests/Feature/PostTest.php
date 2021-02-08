<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\BlogPost;
use App\Models\Comment;

class PostTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testNoBlogPostsWhenNothingInDatabase()
    {
        $response = $this->get('/posts');
        $response->assertSeeText('No blog posts yet!');
    }

    /**
     * 
     */
    public function testSee1BlogPostWhenThereIs1WithNoComments()
    {
        // Arrange
        $post = $this->createDummyBlogPost();

        // Act
        $response = $this->get('/posts');

        // Assert
        $response->assertSeeText('New title');
        $response->assertSeeText('No comments yet.');

        $this->assertDatabaseHas('blog_posts', [
            'title' => 'New title'
        ]);
    }

    /**
     * 
     */
    public function testSee1BlogPostWithComments()
    {
        $post = $this->createDummyBlogPost();
        Comment::factory(4)->create([
            'blog_post_id' => $post->id,
        ]);

        $response = $this->get('/posts');
        $response->assertSeeText('4 comments');
    }

    /**
     * 
     */
    public function testStoreValid()
    {
        $params = [
            'title' => 'Valid title',
            'content' => 'At least 10 characters'
        ];

        $this->actingAs($this->user())
            ->post('/posts', $params)
            ->assertStatus(302)
            ->assertSessionHas('status');

        $this->assertEquals(session('status'), 'The blog post was created');
    }

    /**
     * 
     */
    public function testStoreFail()
    {
        $params = [
            'title' => 'x',
            'content' => 'x'
        ];

        $this->actingAs($this->user())
            ->post('/posts', $params)
            ->assertStatus(302)
            ->assertSessionHas('errors');
            
        $messages = session('errors')->getMessages();
        //dd($messages->getMessages());
        $this->assertEquals($messages['title'][0], 'The title must be at least 5 characters.');
        $this->assertEquals($messages['content'][0], 'The content must be at least 10 characters.');
    }

    /**
     * 
     */
    public function testUpdateValid()
    {
        // Arrange
        $user = $this->user();
        $post = $this->createDummyBlogPost($user->id);
        //dd($post->toArray());
        $this->assertDatabaseHas('blog_posts', $post->getAttributes());
        
        $params = [
            'title' => 'Valid title',
            'content' => 'Content is long enough'
        ];

        $this->actingAs($user)
            ->put("/posts/{$post->id}", $params)
            ->assertStatus(302)
            ->assertSessionHas('status');

        //dd(session('status'));    
        $this->assertEquals(session('status'), 'Blog post was updated!');

        $this->assertDatabaseMissing('blog_posts', $post->getAttributes());        
        $this->assertDatabaseHas('blog_posts', [
            'title' => 'Valid title'
        ]);  
    }

    /**
     * 
     */
    public function testDelete()
    {
        $user = $this->user();
        $post = $this->createDummyBlogPost($user->id);

        $this->assertDatabaseHas('blog_posts', $post->getAttributes());

        $this->actingAs($user)
            ->delete("/posts/{$post->id}")
            ->assertStatus(302)
            ->assertSessionHas('status');
            
        $this->assertEquals(session('status'), 'Blog post was deleted!');
        //$this->assertDatabaseMissing('blog_posts', $post->getAttributes());
        $this->assertSoftDeleted('blog_posts', $post->getAttributes());
    }

    /**
     * 
     */
    private function createDummyBlogPost($userId = null): BlogPost
    {
        // Arrange
        // $post = new BlogPost();
        // $post->title = 'New title';
        // $post->content = 'Content of the blog post';
        // $post->save();
        
        return BlogPost::factory()->newTitle()->create([
            'user_id' => $userId ?? $this->user()->id,
        ]);
        //return $post;
    }
}
