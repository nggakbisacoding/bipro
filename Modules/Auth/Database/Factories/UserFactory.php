<?php

namespace Modules\Auth\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Auth\Entities\User;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
            'password' => $this->faker->password(),
            'type' => $this->faker->randomElement([User::TYPE_ADMIN, User::TYPE_USER]),
            'active' => $this->faker->randomElement([1, 0]),
            'timezone' => $this->faker->timezone(),
            'last_login_at' => $this->faker->dateTime(),
            'last_login_ip' => $this->faker->ipv4(),
            'to_be_logged_out' => $this->faker->randomElement([1, 0]),
        ];
    }

    /**
     * @return UserFactory
     */
    public function admin()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => User::TYPE_ADMIN,
            ];
        });
    }

    /**
     * @return UserFactory
     */
    public function user()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => User::TYPE_USER,
            ];
        });
    }

    /**
     * @return UserFactory
     */
    public function active()
    {
        return $this->state(function (array $attributes) {
            return [
                'active' => true,
            ];
        });
    }

    /**
     * @return UserFactory
     */
    public function inactive()
    {
        return $this->state(function (array $attributes) {
            return [
                'active' => false,
            ];
        });
    }

    /**
     * @return UserFactory
     */
    public function confirmed()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => now(),
            ];
        });
    }

    /**
     * @return UserFactory
     */
    public function unconfirmed()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }

    /**
     * @return UserFactory
     */
    public function passwordExpired()
    {
        return $this->state(function (array $attributes) {
            return [
                'password_changed_at' => now()->subYears(5),
            ];
        });
    }

    /**
     * @return UserFactory
     */
    public function deleted()
    {
        return $this->state(function (array $attributes) {
            return [
                'deleted_at' => now(),
            ];
        });
    }
}

