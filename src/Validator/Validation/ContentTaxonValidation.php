<?php

namespace App\Validator\Validation;

use App\Validator\Constraints\ValidTaxon;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Required;
use Symfony\Component\Validator\Constraints\Sequentially;
use Symfony\Component\Validator\Constraints\Type;

class ContentTaxonValidation implements ValidationInterface
{
    public function getConstraints(?int $entityId = null): Collection
    {
        return new Collection([
            'taxon' => new Required([
                new Sequentially([
                    new NotNull(),
                    new Type('int'),
                    new ValidTaxon(),
                ]),
            ]),
        ]);
    }
}