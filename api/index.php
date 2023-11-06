<?php

require_once 'util/JsonResponse.php'; 
require_once 'controller/CourseController.php';
require_once 'controller/UnitController.php';
require_once 'controller/UserController.php';
require_once 'controller/ActivityController.php';
require_once 'controller/AuthController.php';


use \util\JsonResponse;


header('Content-Type: application/json');
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

$controller = $_GET['controller'] ?? null;
$action = $_GET['action'] ?? null;



if (!empty($controller) && !empty($action)) {
    // Define un mapeo de rutas a controladores y acciones
    $routes = [
        'course' => '\\controller\\CourseController', // Agrega otros controladores aquí
        'unit' => '\\controller\\UnitController',
        'user' => '\\controller\\UserController',
        'activity' => '\\controller\\ActivityController',
        'auth' => '\\controller\\AuthController'
    ];

    // Comprueba si el controlador y la acción existen en el mapeo
    if (isset($routes[$controller]) && class_exists($routes[$controller])) {

        $controllerClass = $routes[$controller];
        $controllerInstance = new $controllerClass();

        // En función del método HTTP y la acción, llama al método correspondiente
        $method = $_SERVER['REQUEST_METHOD'];
        switch ($method) {
            case 'GET':
                if ($action === 'listar') {
                    $controllerInstance->all();
                } else if ($action === 'obtener') {
                    $controllerInstance->readById();
                } else {
                    JsonResponse::send(400, 'Acción no válida o parámetros incorrectos', [], 'ERROR');
                }
                break;
            case 'POST':
                if ($action === 'crear') {
                    $controllerInstance->create();
                } else if ($action === 'login') {
                    $controllerInstance->login();
                } else {
                    JsonResponse::send(400, 'Acción no válida', [], 'ERROR');
                }
                break;
            case 'PUT':
                if ($action === 'actualizar') {
                    $controllerInstance->update();
                } else {
                    JsonResponse::send(400, 'Acción no válida', [], 'ERROR');
                }
                break;
            case 'DELETE':
                if ($action === 'eliminar') {
                    $controllerInstance->delete();
                } else {
                    JsonResponse::send(400, 'Acción no válida', [], 'ERROR');
                }
                break;
            default:
                JsonResponse::send(405, 'Método no válido', [], 'ERROR');
                break;
        }
    } else {
        JsonResponse::send(404, 'Controlador no válido', [
            'controlador' => $controller,
            'action' => $action,
            'route' => $routes[$controller],
            'controller_exits' => class_exists($routes[$controller])
        ], 'ERROR');
    }
} else {
    JsonResponse::send(400, 'Parámetros "controller" y "action" son requeridos en la URL', [], 'ERROR');
}
