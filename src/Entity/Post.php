<?php

namespace App\Entity;

use App\Repository\PostRepository;
use App\Traits\TimeInterface;
use App\Traits\TimeTrait;
use App\Traits\UserInterface;
use App\Traits\UserTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PostRepository::class)]
#[ORM\Table(name: 'posts')]
#[ORM\HasLifecycleCallbacks]
class Post implements TimeInterface, UserInterface
{
    use TimeTrait;
    use UserTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['posts'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['posts'])]
    private ?string $title = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Groups(['posts'])]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['posts'])]
    private ?string $body = null;

    #[ORM\Column]
    #[Groups(['posts'])]
    private ?bool $published = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        $this->setDefaultSlug();

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(?string $body): static
    {
        $this->body = $body;

        return $this;
    }

    public function isPublished(): ?bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): static
    {
        $this->published = $published;

        return $this;
    }

    private function setDefaultSlug(): void
    {
        $this->slug = $this->slug ?? $this->title;
    }
}
