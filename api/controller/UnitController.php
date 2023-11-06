<?php

namespace controller;

require_once 'model/Unit.php';
require_once 'util/JsonResponse.php';
require_once 'dao/impl/UnitMySqlDao.php';
require_once 'dao/UnitDao.php';
require_once 'controller/BaseController.php';
require_once 'validation/Request.php';

use validation\Request;
use controller\BaseController;
use dao\impl\UnitMySqlDao;
use util\JsonResponse;
use dao\UnitDao;
use model\Unit;

class UnitController extends BaseController
{

    private UnitDao $unitDao;

    public function __construct()
    {
        $this->unitDao = new UnitMySqlDao();
    }

    public function all()
    {
        $units = $this->unitDao->allUnits();
        JsonResponse::send(200, 'Listado de unidades', $units, 'UNITS_GET_OK', 200);
    }

    public function create()
    {
        $requestData = json_decode(file_get_contents('php://input'), true);

        Request::validate($requestData, Unit::$rules);

        $unit = Unit::fillUnitFromRequestData($requestData);

        $unitCreated = $this->unitDao->createUnit($unit);

        if ($unitCreated) {
            JsonResponse::send(200, 'Registro satisfactorio', [$unitCreated->getJson()], 'UNIT_INSERT_OK', 201);
        } else {
            JsonResponse::send(500, 'Registro erróneo', [], 'UNIT_INSERT_ERROR', 500);
        }
    }

    public function readById()
    {
        $id = $_GET['id'] ?? null;

        $this->validateIdParameter($id);

        $unitId = (int) $id;

        $unit = $this->unitDao->readUnitById($unitId);

        if ($unit) {
            JsonResponse::send(200, 'Búsqueda satisfactoria', $unit->getJsonWithRelations(), 'UNIT_GET_OK', 200);
        } else {
            JsonResponse::send(404, 'Unidad no encontrada', [], 'UNIT_GET_ERROR', 404);
        }
    }

    public function update()
    {
        $id = $_GET['id'] ?? null;

        $this->validateIdParameter($id);

        $requestData = json_decode(file_get_contents('php://input'), true);

        Request::validate($requestData, Unit::$rules);

        $unit = Unit::fillUnitFromRequestData($requestData);
        $unit->setId($id);

        $unitUpdated = $this->unitDao->updateUnit($unit);

        if ($unitUpdated) {
            JsonResponse::send(200, 'Unidad actualizada exitosamente', [$unitUpdated->getJson()], 'UNIT_UPDATE_OK', 200);
        } else {
            JsonResponse::send(500, 'Error al actualizar la unidad', [], 'UNIT_UPDATE_ERROR', 500);
        }
    }

    public function delete()
    {
        $id = $_GET['id'] ?? null;

        $this->validateIdParameter($id);

        $unitDeleted = $this->unitDao->deleteUnit($id);

        if ($unitDeleted) {
            JsonResponse::send(200, 'Unidad eliminada exitosamente', $unitDeleted->getJson(), 'UNIT_DELETE_OK', 200);
        } else {
            JsonResponse::send(301, 'Error al eliminar la unidad', [], 'UNIT_DELETE_ERROR', 500);
        }
    }
}
