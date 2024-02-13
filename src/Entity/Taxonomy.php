<?php

namespace App\Entity;

use App\Repository\TaxonomyRepository;
use App\Traits\TimeInterface;
use App\Traits\TimeTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TaxonomyRepository::class)]
#[ORM\Table(name: 'taxonomies')]
#[ORM\HasLifecycleCallbacks]
class Taxonomy implements TimeInterface
{
    use TimeTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['taxonomies', 'taxon_bags', 'taxa'])]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Groups(['taxonomies', 'taxon_bags', 'taxa'])]
    private ?string $name = null;

    #[ORM\OneToMany(targetEntity: Taxon::class, mappedBy: 'taxonomy')]
    private Collection $taxa;

    public function __construct()
    {
        $this->taxa = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection<int, Taxon>
     */
    public function getTaxa(): Collection
    {
        return $this->taxa;
    }

    public function addTaxon(Taxon $taxon): static
    {
        if (!$this->taxa->contains($taxon)) {
            $this->taxa->add($taxon);
            $taxon->setTaxonomy($this);
        }

        return $this;
    }

    public function removeTaxon(Taxon $taxon): static
    {
        if ($this->taxa->removeElement($taxon)) {
            // set the owning side to null (unless already changed)
            if ($taxon->getTaxonomy() === $this) {
                $taxon->setTaxonomy(null);
            }
        }

        return $this;
    }
}
