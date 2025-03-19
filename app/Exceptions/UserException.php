<?php

namespace App\Exceptions;

use Exception;

class UserException extends Exception
{
    /**
     * Custom message for user-related exceptions.
     *
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct($message = "A user-related error occurred.", $code = 0)
    {
        parent::__construct($message, $code);
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function render()
    {
        return response()->json([
            'error' => true,
            'message' => $this->getMessage(),
        ], 400);
    }
}