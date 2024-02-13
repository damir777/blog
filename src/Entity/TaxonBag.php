<?php

namespace App\Entity;

use App\Repository\TaxonBagRepository;
use App\Traits\TimeInterface;
use App\Traits\TimeTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TaxonBagRepository::class)]
#[ORM\Table(name: 'taxon_bags')]
#[ORM\HasLifecycleCallbacks]
class TaxonBag implements TimeInterface
{
    use TimeTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['taxon_bags'])]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['taxon_bags'])]
    private ?Taxonomy $taxonomy = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Groups(['taxon_bags'])]
    private ?string $name = null;

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }
}
