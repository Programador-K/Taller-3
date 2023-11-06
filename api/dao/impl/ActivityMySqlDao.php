<?php

namespace dao\impl;

require_once 'model/Activity.php';
require_once 'model/Unit.php';
require_once 'model/Course.php';

require_once 'dao/ActivityDao.php';
require_once 'util/DatabaseConnection.php';

use \util\DatabaseConnection;
use \model\Activity;
use \model\Unit;
use \model\Course;
use \dao\ActivityDao;
use \PDO;

class ActivityMySqlDao implements ActivityDao
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = DatabaseConnection::getInstance()->getConnection();
    }

    public function createActivity(Activity $activity): ?Activity
    {
        $sql = "INSERT INTO activities (unit_id, title, description, activitiescol) 
                VALUES (:unit_id, :title, :description, :activitiescol)";

        $stmt = $this->pdo->prepare($sql);

        $unitId = $activity->getUnit()->getId();
        $title = $activity->getTitle();
        $description = $activity->getDescription();
        $activitiescol = $activity->getActivitiescol();

        $stmt->bindParam(':unit_id', $unitId);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':activitiescol', $activitiescol);


        if ($stmt->execute()) {
            $id = $this->pdo->lastInsertId();
            $activity->setId($id);
            return $activity;
        }

        return null;
    }

    public function readActivityById(int $id): ?Activity
    {
        //$sql = "SELECT * FROM activities WHERE id = :id";
        $sql = "SELECT
        a.id AS activity_id,
        a.unit_id AS activity_unit_id,
        a.title AS activity_title,
        a.description AS activity_description,
        a.activitiescol AS activity_activitiescol,
        u.id AS unit_id,
        u.course_id AS unit_course_id,
        u.user_id AS unit_user_id,
        u.name AS unit_name,
        u.introduction AS unit_introduction,
        u.creation_date AS unit_creation_date,
        u.creation_time AS unit_creation_time,
        u.active AS unit_active
        FROM activities a
        LEFT JOIN units u ON a.unit_id = u.id where a.id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $activity = new Activity();
            $activity->setId((int)$row['activity_id']);
            $activity->setTitle($row['activity_title']);
            $activity->setDescription($row['activity_description']);
            $activity->setActivitiescol($row['activity_activitiescol']);

            $unit = new Unit();
            $unit->setId((int)$row['unit_id']);
            $unit->getCourse()->setId((int)$row['unit_course_id']);
            $unit->getUser()->setId((int)$row['unit_user_id']);
            $unit->setName($row['unit_name']);
            $unit->setIntroduction($row['unit_introduction']);
            $unit->setCreationDate($row['unit_creation_date']);
            $unit->setCreationTime($row['unit_creation_time']);
            $unit->setActive($row['unit_active']);

            $activity->setUnit($unit);

            return $activity;
        } else {
            return null;
        }
    }

    public function updateActivity(Activity $activity): ?Activity
    {
        $sql = "UPDATE activities SET unit_id = :unit_id, title = :title, description = :description, 
                activitiescol = :activitiescol WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        $id = $activity->getId();
        $unitId = $activity->getUnit()->getId();
        $title = $activity->getTitle();
        $description = $activity->getDescription();
        $activitiescol = $activity->getActivitiescol();

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':unit_id', $unitId);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':activitiescol', $activitiescol);

        if ($stmt->execute()) {
            return $activity;
        }

        return null;
    }

    public function deleteActivity(int $id): ?Activity
    {
        $sql = "DELETE FROM activities WHERE id = :id";
        $activity = $this->readActivityById($id);
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        if ($stmt->execute()) {
            return $activity;
        }
        return null;
    }

    public function allActivities(): array
    {
        $sql = "SELECT
        a.id AS activity_id,
        a.unit_id AS activity_unit_id,
        a.title AS activity_title,
        a.description AS activity_description,
        a.activitiescol AS activity_activitiescol,
        u.id AS unit_id,
        u.course_id AS unit_course_id,
        u.user_id AS unit_user_id,
        u.name AS unit_name,
        u.introduction AS unit_introduction,
        u.creation_date AS unit_creation_date,
        u.creation_time AS unit_creation_time,
        u.active AS unit_active
        FROM activities a
        LEFT JOIN units u ON a.unit_id = u.id;";


        $stmt = $this->pdo->query($sql);
        $activities = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $activity = new Activity();
            $activity->setId((int)$row['activity_id']);
            $activity->setTitle($row['activity_title']);
            $activity->setDescription($row['activity_description']);
            $activity->setActivitiescol($row['activity_activitiescol']);

            $unit = new Unit();
            $unit->setId((int)$row['unit_id']);
            $unit->getCourse()->setId((int)$row['unit_course_id']);
            $unit->getUser()->setId((int)$row['unit_user_id']);
            $unit->setName($row['unit_name']);
            $unit->setIntroduction($row['unit_introduction']);
            $unit->setCreationDate($row['unit_creation_date']);
            $unit->setCreationTime($row['unit_creation_time']);
            $unit->setActive($row['unit_active']);

            $activity->setUnit($unit);

            $activities[] = $activity->getJsonWithRelations();
        }

        

        return $activities;
    }
}
