<?php

namespace Modules\Auth\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Modules\Auth\Entities\User;
use Modules\Auth\Services\UserService;

/**
 * Class UnusedPassword.
 */
class UnusedPassword implements Rule
{
    protected $user;

    /**
     * Create a new rule instance.
     *
     * UnusedPassword constructor.
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     */
    public function passes($attribute, $value): bool
    {
        // Option is off
        if (! config('boilerplate.access.user.password_history')) {
            return true;
        }

        if (! $this->user instanceof User) {
            if (is_numeric($this->user)) {
                $this->user = resolve(UserService::class)->getById($this->user);
            } else {
                $this->user = resolve(UserService::class)->getByColumn($this->user, 'email');
            }
        }

        if (! $this->user || null === $this->user) {
            return false;
        }

        $histories = $this->user
            ->passwordHistories()
            ->take(config('boilerplate.access.user.password_history'))
            ->orderBy('id', 'desc')
            ->get();

        foreach ($histories as $history) {
            if (Hash::check($value, $history->password)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return __('You can not set a password that you have previously used within the last :num times.', [
            'num' => config('boilerplate.access.user.password_history'),
        ]);
    }
}
