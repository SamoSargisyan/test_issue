<?php

namespace App\Exceptions;

abstract class AbstractException extends \Exception implements CustomException
{

    public function getFieldException(): ?string
    {
        return null;
    }

    public function getData()
    {
        $error = [
            'code' => $this->getCodeException(),
            'message' => $this->getMessageException(),
            'description' => $this->getDescriptionException(),
        ];

        if($field = $this->getFieldException()) {
            $error['field'] = $field;
        }

        return [
            $error,
        ];
    }
}
