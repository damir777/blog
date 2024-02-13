<?php

namespace App\Traits;

interface TimeInterface
{
    public function getCreatedAt(): ?\DateTimeImmutable;

    public function setCreatedAt(): static;

    public function getUpdatedAt(): ?\DateTimeImmutable;

    public function setUpdatedAt(): static;
}