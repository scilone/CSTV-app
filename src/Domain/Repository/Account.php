<?php

namespace App\Domain\Repository;

use App\Infrastructure\CacheRaw;
use App\Infrastructure\CurlOO;
use App\Infrastructure\MysqliDecorator;
use App\Infrastructure\SqlConnection;

class Account
{
    /**
     * @var SqlConnection
     */
    private $connection;

    public function __construct(SqlConnection $connection)
    {
        $this->connection = $connection;
    }

    public function create(string $username, string $password, array $data): int
    {
        $this->connection->get()->query(
            'INSERT INTO `app_users` (`username`, `password`, `data`) ' .
            'VALUES ("' . $username . '", "' . password_hash($password, PASSWORD_DEFAULT) . '", \'' . serialize($data) .
            '\');'
        );

        return $this->connection->get()->insert_id;
    }

    public function getFromId(int $id): array
    {
        $res = $this->connection->get()->query(
            'SELECT * FROM `app_users` WHERE `id` = ' . $id
        )->fetch_assoc();

        if (empty($res['data']) === false) {
            $res['data'] = unserialize($res['data']);
        }

        return $res;
    }

    public function addDataForUser(int $userId, array $data): void
    {
        $userInfo = $this->getFromId($userId);
        $dataToStore = $data + $userInfo['data'];

        $this->connection->get()->query(
            'UPDATE `app_users` SET `data` = \'' . serialize($dataToStore) . '\' WHERE `app_users`.`id` = ' . $userId
        );
    }

    public function getFromUsername(string $username): array
    {
        $res = $this->connection->get()->query(
            'SELECT * FROM `app_users` WHERE `username` = "' . $username . '"'
        );

        if ($res === false) {
            return [];
        }

        $res = $res->fetch_assoc();

        if (empty($res['data']) === false) {
            $res['data'] = unserialize($res['data']);
        }

        return $res;
    }
}
