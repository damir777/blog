<?php

namespace App\Validator\Constraints;

use App\Repository\UniquenessRepositoryInterface;
use App\Service\SluggerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueFieldValidator extends ConstraintValidator
{
    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly SluggerService $slugger)
    {
    }

    /**
     * @param UniqueField $constraint
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        /** @var class-string $class */
        $class = $constraint->class;
        $value = $this->slugger->slug($value);

        /** @var UniquenessRepositoryInterface $repository */
        $repository = $this->entityManager->getRepository($class);
        $entity = $repository->findOneByField($value, $constraint->entityId);

        if ($entity !== null) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ class }}', $constraint->class)
                ->setParameter('{{ field }}', $constraint->field)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}