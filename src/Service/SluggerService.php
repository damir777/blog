<?php

namespace App\Service;

use Symfony\Component\String\Slugger\SluggerInterface;

class SluggerService
{
    public function __construct(private readonly SluggerInterface $slugger)
    {
    }

    public function slug(string $string): string
    {
        return $this->slugger->slug($string, '-', 'en')->lower();
    }
}