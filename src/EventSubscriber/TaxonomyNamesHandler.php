<?php

namespace App\EventSubscriber;

use App\Entity\TaxonBag;
use App\Entity\Taxonomy;
use App\Service\SluggerService;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;

class TaxonomyNamesHandler implements EventSubscriber
{
    public function __construct(private readonly SluggerService $slugger)
    {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    public function prePersist(PrePersistEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Taxonomy && !$entity instanceof TaxonBag) {
            return;
        }

        $this->setName($entity);
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Taxonomy && !$entity instanceof TaxonBag) {
            return;
        }

        $this->setName($entity);
    }

    private function setName(Taxonomy|TaxonBag $entity): void
    {
        /** @var string $name */
        $name = $entity->getName();
        $entity->setName($this->slugger->slug($name));
    }
}