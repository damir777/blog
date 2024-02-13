<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class UniqueField extends Constraint
{
    public string $class;

    public string $field;

    public ?int $entityId;

    public function __construct(
        string $class,
        string $field,
        ?int $entityId = null
    ) {
        parent::__construct();

        $this->class = $class;
        $this->field = $field;
        $this->entityId = $entityId;
    }

    public string $message = '{{ class }} with "{{ field }}" "{{ value }}" already exists.';
}