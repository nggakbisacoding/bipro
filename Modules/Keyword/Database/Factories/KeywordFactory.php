<?php

namespace Modules\Keyword\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Keyword\Entities\Keyword;

class KeywordFactory extends Factory
{
    protected $model = \Modules\Keyword\Entities\Keyword::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'type' => $this->faker->randomElement([
                Keyword::TYPE_ACCOUNT,
                Keyword::TYPE_KEYWORD,
            ]),
            'source' => $this->faker->randomElement([
                Keyword::SOURCE_FACEBOOK,
                Keyword::SOURCE_INSTAGRAM,
                Keyword::SOURCE_TIKTOK,
                Keyword::SOURCE_YOUTUBE,
            ]),
            'status' => $this->faker->randomElement([1, 0]),
            'last_post' => $this->faker->date(),
        ];
    }
}
