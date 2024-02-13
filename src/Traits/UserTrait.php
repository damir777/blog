<?php

namespace App\Traits;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait UserTrait
{
    #[ORM\ManyToOne(fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['posts', 'content_taxa'])]
    private ?User $createdBy = null;

    #[ORM\ManyToOne(fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['posts', 'content_taxa'])]
    private ?User $updatedBy = null;

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getUpdatedBy(): ?User
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?User $updatedBy): static
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }
}