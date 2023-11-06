<?php

namespace dao;

require_once './model/Unit.php';

use \model\Unit;

interface UnitDao
{
    public function createUnit(Unit $unit): ?Unit;
    public function readUnitById(int $id): ?Unit;
    public function updateUnit(Unit $unit): ?Unit;
    public function deleteUnit(int $id): ?Unit;
    public function allUnits(): array;
}
