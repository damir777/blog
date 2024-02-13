<?php

namespace App\Validator\Validation;

use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Required;
use Symfony\Component\Validator\Constraints\Type;

class TaxonValidation implements ValidationInterface
{
    public function getConstraints(?int $entityId = null): Collection
    {
        return new Collection([
            'title' => new Required([
                new NotNull(),
                new Type('string'),
                new Length([
                    'min' => 3,
                    'max' => 255,
                    'normalizer' => 'trim',
                ]),
            ]),
        ]);
    }
}