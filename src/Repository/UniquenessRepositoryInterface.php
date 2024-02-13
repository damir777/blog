<?php

namespace App\Repository;

interface UniquenessRepositoryInterface
{
    public function findOneByField(string $value, ?int $excludedId = null): ?object;
}