<?php

// Entity/Status.php
namespace App\Entity;

use App\Repository\StatusRepository;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\String\Slugger\AsciiSlugger;

#[ORM\Entity(repositoryClass: StatusRepository::class)]
#[OA\Schema(
    schema: 'Status',
    description: 'Статус пользователя'
)]
class Status
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['status:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Groups(['status:read'])]
    private string $name = '';

    #[ORM\Column(length: 150, unique: true)]
    #[Groups(['status:read'])]
    private string $slug = '';

    #[ORM\Column(length: 25)]
    #[Groups(['status:read'])]
    private string $color = '#000000';

    #[ORM\Column(length: 25)]
    #[Groups(['status:read'])]
    private string $bgColor = '#FFFFFF';

    #[ORM\Column(length: 100, nullable: true)]
    #[Groups(['status:read'])]
    private ?string $iconUrl = null;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['status:read'])]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        if ($this->slug === '') {
            $slugger = new AsciiSlugger();
            $this->slug = $slugger->slug($name)->lower()->toString();
        }

        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function setColor(string $color): static
    {
        $this->color = $color;
        return $this;
    }

    public function getBgColor(): string
    {
        return $this->bgColor;
    }

    public function setBgColor(string $bgColor): static
    {
        $this->bgColor = $bgColor;
        return $this;
    }

    public function getIconUrl(): ?string
    {
        return $this->iconUrl;
    }

    public function setIconUrl(?string $iconUrl): static
    {
        $this->iconUrl = $iconUrl;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
