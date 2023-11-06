<?php

namespace dao;

require_once 'model/Activity.php';

use \model\Activity;

interface ActivityDao
{
    public function createActivity(Activity $activity): ?Activity;
    public function readActivityById(int $id): ?Activity;
    public function updateActivity(Activity $activity): ?Activity;
    public function deleteActivity(int $id): ?Activity;
    public function allActivities(): array;
}
