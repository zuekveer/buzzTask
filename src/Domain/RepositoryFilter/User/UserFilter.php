<?php

declare(strict_types=1);

namespace App\Domain\RepositoryFilter\User;

class UserFilter
{
    /**
     * @param bool|null $state
     */
    public function __construct(
        public ?bool $state = true,
    ) {
    }
}
