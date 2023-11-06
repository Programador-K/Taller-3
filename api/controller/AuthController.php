<?php

namespace controller;

require_once 'model/Activity.php';
require_once 'util/JsonResponse.php';
require_once 'dao/impl/AuthMySqlDao.php';
require_once 'dao/AuthDao.php';
require_once 'controller/BaseController.php';
require_once 'validation/Request.php';

use validation\Request;
use controller\BaseController;
use dao\impl\AuthMySqlDao;
use util\JsonResponse;
use dao\AuthDao;
use model\User;

class AuthController extends BaseController
{
    private AuthDao $authDao;

    public function __construct()
    {
        $this->authDao = new AuthMySqlDao();
    }

    public function login(){

        $requestData = json_decode(file_get_contents('php://input'), true);

        $rules = [
            'email' => 'required|email',
            'password' => 'required'
        ];

        Request::validate($requestData, $rules);

        $user = User::fillUserFromRequestData($requestData);

        $userLogin = $this->authDao->login($user);

        if ($userLogin != null) {
            JsonResponse::send(200, 'Inicio de sesion satisfactorio', $userLogin->getJson(), 'AUTH_LOGIN_OK', 201);
        } else {
            JsonResponse::send(500, 'Inicio de sesion erroneo', ['credenciales' => 'incorrectas'], 'AUTH_LOGIN_ERROR', 500);
        }
    }

}