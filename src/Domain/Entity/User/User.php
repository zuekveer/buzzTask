<?php

declare(strict_types=1);

namespace App\Domain\Entity\User;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'user')]
#[ORM\HasLifecycleCallbacks]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private ?int $id;

    #[ORM\Column(type: 'boolean', options: ['default' => 0])]
    private bool $state;

    #[ORM\Column(type: 'string', unique: true, nullable: true)]
    private ?string $username;

    #[ORM\Column(type: 'string', options: ['default' => ''])]
    private string $password;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $updatedAt;

    public function __construct(
        ?string $username,
        string $password,
        ?\DateTimeImmutable $createdAt = null,
        bool $state = true,
    ) {
        $this->id = null;
        $this->state = $state;
        $this->username = $username;
        $this->password = $password;
        $this->createdAt = $createdAt ?? new \DateTimeImmutable();
        $this->updatedAt = $createdAt ?? new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function setUpdatedAt(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getState(): bool
    {
        return $this->state;
    }
}
