<?php

namespace App\Validator\Validation;

use App\Entity\TaxonBag;
use App\Validator\Constraints\UniqueField;
use App\Validator\Constraints\ValidTaxonomy;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Required;
use Symfony\Component\Validator\Constraints\Sequentially;
use Symfony\Component\Validator\Constraints\Type;

class TaxonBagValidation implements ValidationInterface
{
    public function getConstraints(?int $entityId = null): Collection
    {
        $constraints = new Collection([
            'name' => new Required([
                new NotNull(),
                new Type('string'),
                new Length([
                    'min' => 3,
                    'max' => 255,
                    'normalizer' => 'trim',
                ]),
                new UniqueField(TaxonBag::class, 'name', $entityId)
            ]),
        ]);

        if ($entityId === null) {
            $constraints->fields['taxonomy'] = new Required([
                new Sequentially([
                    new NotNull(),
                    new Type('int'),
                    new ValidTaxonomy(),
                ]),
            ]);
        }

        return $constraints;
    }
}