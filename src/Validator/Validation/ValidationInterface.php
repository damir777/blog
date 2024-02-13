<?php

namespace App\Validator\Validation;

use Symfony\Component\Validator\Constraints\Collection;

interface ValidationInterface
{
    public function getConstraints(?int $entityId = null): Collection;
}