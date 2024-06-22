<?php

namespace Modules\Auth\Database\Factories;

use Modules\Auth\Entities\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Auth\Entities\User;

/**
 * Class RoleFactory.
 */
class RoleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Role::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'type' => $this->faker->randomElement([User::TYPE_ADMIN, User::TYPE_USER]),
            'name' => $this->faker->word,
        ];
    }
}
