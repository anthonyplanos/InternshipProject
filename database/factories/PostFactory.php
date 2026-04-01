<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $category = Category::firstOrCreate([
            'name' => ucfirst($this->faker->word()),
        ]);

        return [
            'user_id' => User::factory(),
            'category' => $category->name,
            'category_id' => $category->id,
            'content' => $this->faker->realTextBetween(80, 220),
            'attachment' => null,
        ];
    }
}
