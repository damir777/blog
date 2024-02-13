<?php

namespace App\Validator\Constraints;

use App\Service\TaxonService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ValidTaxonValidator extends ConstraintValidator
{
    public function __construct(private readonly TaxonService $service)
    {
    }

    /**
     * @param ValidTaxonomy $constraint
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        $taxonomy = $this->service->findById($value);

        if ($taxonomy === null) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}