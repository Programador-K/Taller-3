<?php

namespace controller;

require_once 'model/Activity.php';
require_once 'util/JsonResponse.php';
require_once 'dao/impl/ActivityMySqlDao.php';
require_once 'dao/ActivityDao.php';
require_once 'controller/BaseController.php';
require_once 'validation/Request.php';

use validation\Request;
use controller\BaseController;
use dao\impl\ActivityMySqlDao;
use util\JsonResponse;
use dao\ActivityDao;
use model\Activity;

class ActivityController extends BaseController
{
    private ActivityDao $activityDao;

    public function __construct()
    {
        $this->activityDao = new ActivityMySqlDao();
    }

    public function all()
    {
        $activities = $this->activityDao->allActivities();
        JsonResponse::send(200, 'Listado de actividades', $activities, 'ACTIVITIES_GET_OK', 200);
    }

    public function create()
    {
        $requestData = json_decode(file_get_contents('php://input'), true);

        Request::validate($requestData, Activity::$rules);

        $activity = Activity::fillActivityFromRequestData($requestData);

        $activityCreated = $this->activityDao->createActivity($activity);

        if ($activityCreated) {
            JsonResponse::send(200, 'Registro satisfactorio', [$activityCreated->getJson()], 'ACTIVITY_INSERT_OK', 201);
        } else {
            JsonResponse::send(500, 'Registro erróneo', [], 'ACTIVITY_INSERT_ERROR', 500);
        }
    }

    public function readById()
    {
        $id = $_GET['id'] ?? null;

        $this->validateIdParameter($id);

        $activityId = (int) $id;

        $activity = $this->activityDao->readActivityById($activityId);

        if ($activity) {
            JsonResponse::send(200, 'Búsqueda satisfactoria', $activity->getJsonWithRelations(), 'ACTIVITY_GET_OK', 200);
        } else {
            JsonResponse::send(404, 'Actividad no encontrada', [], 'ACTIVITY_GET_ERROR', 404);
        }
    }

    public function update()
    {
        $id = $_GET['id'] ?? null;

        $this->validateIdParameter($id);

        $requestData = json_decode(file_get_contents('php://input'), true);

        Request::validate($requestData, Activity::$rules);

        $activity = Activity::fillActivityFromRequestData($requestData);
        $activity->setId($id);

        $activityUpdated = $this->activityDao->updateActivity($activity);

        if ($activityUpdated) {
            JsonResponse::send(200, 'Actividad actualizada exitosamente', [$activityUpdated->getJson()], 'ACTIVITY_UPDATE_OK', 200);
        } else {
            JsonResponse::send(500, 'Error al actualizar la actividad', [], 'ACTIVITY_UPDATE_ERROR', 500);
        }
    }

    public function delete()
    {
        $id = $_GET['id'] ?? null;

        $this->validateIdParameter($id);

        $activityDeleted = $this->activityDao->deleteActivity($id);

        if ($activityDeleted) {
            JsonResponse::send(200, 'Actividad eliminada exitosamente', [$activityDeleted->getJson()], 'ACTIVITY_DELETE_OK', 200);
        } else {
            JsonResponse::send(301, 'Error al eliminar la actividad', [], 'ACTIVITY_DELETE_ERROR', 500);
        }
    }
}


