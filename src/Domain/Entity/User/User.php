<?php

declare(strict_types=1);

namespace App\Domain\Entity\User;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'user')]
class User
{
    #[ORM\Id, ORM\Column(type: 'integer', options: ['unsigned' => true]), ORM\GeneratedValue]
    private ?int $id;

    #[ORM\Column(type: 'boolean', options: ['default' => 0])]
    private bool $state;

    #[ORM\Column(type: 'string', unique: true, nullable: true)]
    private ?string $username;

    #[ORM\Column(type: 'string', options: ['default' => ''])]
    private string $password;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?DateTimeImmutable $createdAt;

    public function __construct(
        ?string $username,
        string $password,
        ?DateTimeImmutable $createdAt,
        bool $state = true,
    ) {
        $this->id = null;
        $this->createdAt = $createdAt;

        $this->edit(
            username: $username,
            password: $password,
            state: $state,
        );
    }

    public function edit(?string $username, string $password, bool $state): self
    {
        $this->username = $username;
        $this->password = $password;
        $this->state = $state;

        return $this;
    }

    public function __toString(): string
    {
        return $this->username;
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

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getState(): bool
    {
        return $this->state;
    }
}
