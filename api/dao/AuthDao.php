<?php

namespace dao;

require_once './model/User.php';

use \model\User;

interface AuthDao
{
    public function login(User $user): ?User;
}