<?php

namespace App\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

class ErrorResponse extends JsonResponse
{
    /**
     * @param array|string $errors
     * @param int          $status
     */
    public function __construct($errors, int $status = 400)
    {
        if (is_string($errors)) {
            $errors = [$errors];
        }
        parent::__construct(['errors' => $errors], $status);
    }
}
