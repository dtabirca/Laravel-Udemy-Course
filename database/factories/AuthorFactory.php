<?php

namespace Database\Factories;

use App\Models\Author;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Profile;

class AuthorFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Author::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            //
        ];
    }

    /**
     * 
     */
    public function configure()
    {
        return $this->afterMaking(function (Author $author) {
            $author->profile()->save(Profile::factory()->make());
        })->afterCreating(function (Author $author) {
            $author->profile()->save(Profile::factory()->make());
        });
    }    
}
