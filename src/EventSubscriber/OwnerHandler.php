<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Traits\UserInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use RuntimeException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface as CoreUser;

class OwnerHandler implements EventSubscriber
{
    private bool $isEnabled = true;

    public function __construct(private readonly TokenStorageInterface $storage)
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

        if (!$entity instanceof UserInterface || $this->isEnabled === false) {
            return;
        }

        /** @var User $user */
        $user = $this->getUser();

        $entity->setCreatedBy($user);
        $entity->setUpdatedBy($user);
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof UserInterface) {
            return;
        }

        /** @var User $user */
        $user = $this->getUser();

        $entity->setUpdatedBy($user);
    }

    public function disableSubscriber(): void
    {
        $this->isEnabled = false;
    }

    private function getUser(): CoreUser
    {
        $token = $this->storage->getToken();

        if (!$token instanceof TokenInterface) {
            throw new RuntimeException('Token not set');
        }

        $user = $token->getUser();

        if (!$user instanceof CoreUser) {
            throw new RuntimeException('Unable to get user from token storage');
        }

        return $user;
    }
}