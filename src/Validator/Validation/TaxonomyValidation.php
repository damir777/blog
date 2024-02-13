<?php

namespace App\Validator\Validation;

use App\Entity\Taxonomy;
use App\Validator\Constraints\UniqueField;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Required;
use Symfony\Component\Validator\Constraints\Type;

class TaxonomyValidation implements ValidationInterface
{
    public function getConstraints(?int $entityId = null): Collection
    {
        return new Collection([
            'name' => new Required([
                new NotNull(),
                new Type('string'),
                new Length([
                    'min' => 3,
                    'max' => 255,
                    'normalizer' => 'trim',
                ]),
                new UniqueField(Taxonomy::class, 'name', $entityId)
            ]),
        ]);
    }
}