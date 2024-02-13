<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class ValidTaxon extends Constraint
{
    public string $message = 'Taxon with ID "{{ value }}" not found.';
}