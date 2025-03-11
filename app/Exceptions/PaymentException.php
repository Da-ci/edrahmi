<?php

namespace App\Exceptions;

use Exception;

class PaymentException extends Exception
{
    protected $statusCode;
    protected $errorCode;
    protected $errors;
    protected $detail;

    public function __construct(
        string $message = '',
        string $errorCode = 'GENERAL_ERROR',
        int $statusCode = 500,
        array $errors = [],
        ?Exception $previous = null
    ) {
        parent::__construct($message, 0, $previous);
        $this->errorCode = $errorCode;
        $this->statusCode = $statusCode;
        $this->errors = $errors;
        $this->detail = $errors['gateway_response']['errorMessage'] ?? null;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getDetail(): ?string
    {
        return $this->detail;
    }
}
