<?php

namespace dao\impl;

require_once './util/DatabaseConnection.php';
require_once './dao/AuthDao.php';
require_once './model/User.php';

use util\DatabaseConnection;
use model\User;
use dao\AuthDao;
use PDO;


class AuthMySqlDao implements AuthDao
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = DatabaseConnection::getInstance()->getConnection();
    }

    public function login(User $user): ?User
    {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);

        $email = $user->getEmail();
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            if (password_verify($user->getPassword(), $row['password'])) {
                $user->setId($row['id']);
                $user->setNames($row['names']);
                $user->setLastNames($row['last_names']);
                $user->setEmail($row['email']);
                $user->setDateOfBirth($row['date_of_birth']);
                $user->setUsername($row['username']);
                $user->setPassword($row['password']);
                $user->setPhone($row['phone']);
                return $user;
            }
        }

        return null;
    }
}
