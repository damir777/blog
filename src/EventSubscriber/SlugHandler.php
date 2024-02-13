<?php

namespace App\EventSubscriber;

use App\Entity\Post;
use App\Service\SluggerService;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;

class SlugHandler implements EventSubscriber
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

        if (!$entity instanceof Post) {
            return;
        }

        $this->setSlug($entity);
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Post) {
            return;
        }

        $this->setSlug($entity);
    }

    private function setSlug(Post $entity): void
    {
        /** @var string $slug */
        $slug = $entity->getSlug();
        $entity->setSlug($this->slugger->slug($slug));
    }
}