<?php

namespace Different\Dwfw\database\factories;

use Different\Dwfw\app\Models\File;
use Illuminate\Database\Eloquent\Factories\Factory;

class FileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = File::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'partner_id' => null,
            'original_name' => $this->faker->word(),
            'mime_type' => $this->faker->mimeType,
            'file_path' => $this->faker->word(),
            'access_hash' => $this->faker->word(),
        ];
    }
}
