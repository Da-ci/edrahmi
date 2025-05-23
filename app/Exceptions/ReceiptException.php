<?php

namespace App\Exceptions;

use Exception;

class ReceiptException extends Exception
{
    protected $statusCode;
    protected $errorCode;
    protected $errors;
    protected $detail;

    public function __construct(
        string $message = '',
        string $errorCode = 'RECEIPT_ERROR',
        int $statusCode = 500,
        array $errors = [],
        ?string $detail = '',
        ?Exception $previous = null
    ) {
        parent::__construct($message, 0, $previous);
        $this->errorCode = $errorCode;
        $this->statusCode = $statusCode;
        $this->errors = $errors;
        $this->detail = $detail;
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
