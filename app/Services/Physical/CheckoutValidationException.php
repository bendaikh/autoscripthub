<?php

namespace Fickrr\Services\Physical;

use RuntimeException;

class CheckoutValidationException extends RuntimeException
{
    /** @var array<string, array<int, string>> */
    public array $errors;

    /**
     * @param  array<string, array<int, string>|string>  $errors
     */
    public function __construct(array $errors)
    {
        $normalized = [];
        foreach ($errors as $field => $messages) {
            $normalized[$field] = array_values((array) $messages);
        }
        $this->errors = $normalized;

        $flat = [];
        foreach ($normalized as $messages) {
            foreach ($messages as $message) {
                $flat[] = $message;
            }
        }

        parent::__construct(implode(' ', $flat));
    }
}
