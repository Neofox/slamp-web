<?php
/**
 * Created by PhpStorm.
 * User: Neofox
 * Date: 21/07/2016
 * Time: 16:20
 */

namespace Slamp\Web\Storage;


use Amp\Promise;
use Slamp\Web\Helper\User;

interface UserStorage
{
    public function get(int $id): Promise;

    public function getByName(string $name): Promise;

    public function getByToken(string $token): Promise;

    public function getAll(int $cursor = 0, bool $asc = true, int $limit = 42): Promise;

    public function getByNames(array $names): Promise;

    public function getByIds(array $ids, bool $asc = true): Promise;

    public function add(User $user);
}