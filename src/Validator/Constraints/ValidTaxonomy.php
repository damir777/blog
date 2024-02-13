<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class ValidTaxonomy extends Constraint
{
    public string $message = 'Taxonomy with ID "{{ value }}" not found.';
}