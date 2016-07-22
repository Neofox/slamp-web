<?php
/**
 * Created by PhpStorm.
 * User: Neofox
 * Date: 21/07/2016
 * Time: 16:21
 */

namespace Slamp\Web\Storage;


use Amp\Mysql\Pool;
use Amp\Mysql\ResultSet;
use function Amp\pipe;
use Amp\Promise;
use Amp\Success;
use Slamp\Web\Helper\User;

class MysqlUserStorage implements UserStorage
{
    private $mysql;

    public function __construct(Pool $mysql)
    {
        $this->mysql = $mysql;
    }

    public function get(int $id): Promise
    {
        return pipe($this->mysql->prepare("SELECT * FROM `users` WHERE `id` = ? LIMIT 1", [$id]),
            function (ResultSet $stmt): Promise {
                return $stmt->fetchObject();
            });
    }

    public function getByToken(string $token): Promise
    {
        return pipe($this->mysql->prepare("SELECT * FROM `users` WHERE `token` = ? LIMIT 1", [$token]),
            function (ResultSet $stmt): Promise {
                return $stmt->fetchObject();
            });
    }

    public function add(User $user) : Promise
    {
        return $this->mysql->query("INSERT INTO `users` (`name`, `token`, `slackId`) 
                                                          VALUES  ('{$user->name}', '{$user->token}', '{$user->id}')"
        );
    }

    public function getByName(string $name): Promise
    {
        return pipe($this->mysql->prepare("SELECT * FROM `users` WHERE `name` = ? LIMIT 1",
            [$name]), function (ResultSet $stmt): Promise {
            return $stmt->fetchObject();
        });
    }

    public function getByNames(array $names): Promise
    {
        if (empty($names)) {
            return new Success([]);
        }
        $in = substr(str_repeat(",?", count($names)), 1);

        return pipe($this->mysql->prepare("SELECT * FROM `users` WHERE `name` IN ({$in}) ORDER BY id ASC",
            [$names]), function (ResultSet $stmt): Promise {
            return $stmt->fetchObjects();
        });
    }

    public function getByIds(array $ids, bool $asc = true): Promise
    {
        if (empty($ids)) {
            return new Success([]);
        }
        $in = substr(str_repeat(",?", count($ids)), 1);
        $order = $asc ? "ASC" : "DESC";

        return pipe($this->mysql->prepare("SELECT * FROM `users` WHERE `id` IN ({$in}) ORDER BY id {$order}",
            $ids), function (ResultSet $stmt): Promise {
            return $stmt->fetchObjects();
        });
    }

    public function getAll(int $cursor = 0, bool $asc = true, int $limit = 42): Promise
    {
        $order = $asc ? "ASC" : "DESC";
        $sql = "SELECT * FROM `users` WHERE `id` >= ? ORDER BY id {$order} LIMIT {$limit}";

        return pipe($this->mysql->prepare($sql, [$cursor]), function (ResultSet $stmt): Promise {
            return $stmt->fetchObjects();
        });
    }
}