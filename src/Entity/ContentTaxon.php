<?php

namespace App\Entity;

use App\Repository\ContentTaxonRepository;
use App\Traits\TimeInterface;
use App\Traits\TimeTrait;
use App\Traits\UserInterface;
use App\Traits\UserTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ContentTaxonRepository::class)]
#[ORM\Table(name: 'content_taxa')]
#[ORM\UniqueConstraint(
    name: 'unique_content_taxon_idx',
    columns: ['taxon_bag_id', 'taxon_id', 'post_id']
)]
#[ORM\HasLifecycleCallbacks]
class ContentTaxon implements TimeInterface, UserInterface
{
    use TimeTrait;
    use UserTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['content_taxa'])]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?TaxonBag $taxonBag = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Groups(['content_taxa'])]
    private ?Taxon $taxon = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Post $post = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTaxonBag(): ?TaxonBag
    {
        return $this->taxonBag;
    }

    public function setTaxonBag(?TaxonBag $taxonBag): static
    {
        $this->taxonBag = $taxonBag;

        return $this;
    }

    public function getTaxon(): ?Taxon
    {
        return $this->taxon;
    }

    public function setTaxon(?Taxon $taxon): static
    {
        $this->taxon = $taxon;

        return $this;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): static
    {
        $this->post = $post;

        return $this;
    }
}
