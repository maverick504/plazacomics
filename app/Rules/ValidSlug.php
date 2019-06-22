<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidSlug implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Based on: https://gist.github.com/achmadfatoni/5cb71a93bb7dddcbf3b06de6fa4c0edf
        return preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "The field :attribute isn't valid.";
    }
}
