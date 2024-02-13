<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Validator
{
    private ConstraintViolationListInterface $errors;

    public function __construct(private readonly ValidatorInterface $validator)
    {
    }

    public function validate(array $data, Collection $constraints): array
    {
        $this->errors = $this->validator->validate($data, $constraints);

        return $this->formatErrors();
    }

    private function formatErrors(): array
    {
        $errorMessages = [];

        foreach ($this->errors as $message) {
            $errorMessages['errors'][] = [
                'property' => $message->getPropertyPath(),
                'value' => $message->getInvalidValue(),
                'message' => $message->getMessage(),
            ];
        }

        return $errorMessages;
    }
}