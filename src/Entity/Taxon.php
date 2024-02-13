<?php

namespace App\Entity;

use App\Repository\TaxonRepository;
use App\Traits\TimeInterface;
use App\Traits\TimeTrait;
use App\Traits\UserInterface;
use App\Traits\UserTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TaxonRepository::class)]
#[ORM\Table(name: 'taxa')]
#[ORM\HasLifecycleCallbacks]
class Taxon implements TimeInterface, UserInterface
{
    use TimeTrait;
    use UserTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['taxa', 'content_taxa'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'taxa')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['taxa'])]
    private ?Taxonomy $taxonomy = null;

    #[ORM\Column(length: 255)]
    #[Groups(['taxa', 'content_taxa'])]
    private ?string $title = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTaxonomy(): ?Taxonomy
    {
        return $this->taxonomy;
    }

    public function setTaxonomy(?Taxonomy $taxonomy): static
    {
        $this->taxonomy = $taxonomy;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }
}
