<?php

namespace Database\Factories;

use App\Enums\PageStatus;
use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Page>
 */
class PageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(3);

        return [
            'title' => rtrim($title, '.'),
            'slug' => Str::slug($title),
            'content' => '<p>'.fake()->paragraph().'</p>',
            'meta_title' => null,
            'meta_description' => fake()->optional()->sentence(),
            'og_image' => null,
            'status' => PageStatus::Draft,
            'published_at' => null,
        ];
    }
}
