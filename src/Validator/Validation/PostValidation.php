<?php

namespace App\Validator\Validation;

use App\Entity\Post;
use App\Validator\Constraints\UniqueField;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Optional;
use Symfony\Component\Validator\Constraints\Required;
use Symfony\Component\Validator\Constraints\Type;

class PostValidation implements ValidationInterface
{
    public function getConstraints(?int $entityId = null): Collection
    {
        $constraints = new Collection([
            'slug' => new Optional([
                new NotNull(),
                new Type('string'),
                new Length([
                    'min' => 3,
                    'max' => 255,
                    'normalizer' => 'trim',
                ]),
                new UniqueField(Post::class, 'slug', $entityId),
            ]),
            'body' => new Optional([
                new Type(['null', 'string']),
            ]),
            'published' => new Optional([
                new NotNull(),
                new Type('boolean'),
            ]),
        ]);

        if ($entityId === null) {
            $constraints->fields['title'] = new Required([
                new NotNull(),
                new Type('string'),
                new Length([
                    'min' => 3,
                    'max' => 255,
                    'normalizer' => 'trim',
                ]),
            ]);
        } else {
            $constraints->fields['title'] = new Optional([
                new NotNull(),
                new Type('string'),
                new Length([
                    'min' => 3,
                    'max' => 255,
                    'normalizer' => 'trim',
                ]),
            ]);
        }

        return $constraints;
    }
}