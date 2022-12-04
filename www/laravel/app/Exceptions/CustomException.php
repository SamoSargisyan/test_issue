<?php

namespace App\Exceptions;

interface CustomException
{
    public function getFieldException(): ?string;

    public function getDescriptionException(): string;

    public function getMessageException(): string;

    public function getCodeException(): int;
}
