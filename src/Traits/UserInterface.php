<?php

namespace App\Traits;

use App\Entity\User;

interface UserInterface
{
    public function getCreatedBy(): ?User;

    public function setCreatedBy(?User $createdBy): static;

    public function getUpdatedBy(): ?User;

    public function setUpdatedBy(?User $updatedBy): static;
}