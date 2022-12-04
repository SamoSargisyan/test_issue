<?php

namespace App\Exceptions;

class WrongCredentialsException extends AbstractException
{
    public function getFieldException(): ?string
    {
        return 'password';
    }

    public function getMessageException(): string
    {
        return 'VALIDATION_EXCEPTION';
    }

    public function getCodeException(): int
    {
        return 422;
    }

    public function getDescriptionException(): string
    {
        return __('exception.wrong_credentials');
    }
}
