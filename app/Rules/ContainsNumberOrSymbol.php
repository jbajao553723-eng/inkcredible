<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ContainsNumberOrSymbol implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! preg_match('/[\d!@#$%^&*()_+\-=\[\]{};:\'",.<>\/?\\\\|`~]/u', $value)) {
            $fail('The :attribute must contain at least one number or special character.');
        }
    }
}
