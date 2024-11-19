<?php

declare(strict_types=1);

namespace App\Domain\Repository\User;

use App\Domain\Entity\User\User;
use App\Domain\RepositoryFilter\User\UserFilter;
use DomainException;

interface UserRepositoryInterface
{
    /**
     * Найти пользователя по фильтру
     *
     * @param UserFilter $filter
     *
     * @return array
     */
    public function findUsers(UserFilter $filter): array;

    /**
     * Найти пользователя по ID
     *
     * @param int $id
     *
     * @return User
     *
     * @throws DomainException
     */
    public function findById(int $id): User;

    /**
     * Сохранить пользователя
     *
     * @param User $user
     *
     * @return void
     */
    public function save(User $user): void;

    /**
     * Удалить пользователя
     *
     * @param User $user
     *
     * @return void
     */
    public function delete(User $user): void;
}
