<?php

namespace dao\impl;

require_once 'model/Unit.php';
require_once 'model/Course.php';
require_once 'model/User.php';
require_once 'dao/UnitDao.php';
require_once 'util/DatabaseConnection.php';

use \util\DatabaseConnection;
use \model\Unit;
use \model\Course;
use \model\User;
use \dao\UnitDao;
use \PDO;

class UnitMySqlDao implements UnitDao
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = DatabaseConnection::getInstance()->getConnection();
    }

    public function createUnit(Unit $unit): ?Unit
    {
        $sql = "INSERT INTO units (course_id, user_id, name, introduction, creation_date, creation_time, active) 
                VALUES (:course_id, :user_id, :name, :introduction, :creation_date, :creation_time, :active)";

        $stmt = $this->pdo->prepare($sql);

        $courseId = $unit->getCourse()->getId();
        $userId = $unit->getUser()->getId();
        $name = $unit->getName();
        $introduction = $unit->getIntroduction();
        $creationDate = $unit->getCreationDate();
        $creationTime = $unit->getCreationTime();
        $active = $unit->isActive();

        $stmt->bindParam(':course_id', $courseId);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':introduction', $introduction);
        $stmt->bindParam(':creation_date', $creationDate);
        $stmt->bindParam(':creation_time', $creationTime);
        $stmt->bindParam(':active', $active);

        if ($stmt->execute()) {
            $lastInsertId = $this->pdo->lastInsertId();
            $unit->setId($lastInsertId);

            return $unit;
        } else {
            return null;
        }
    }

    public function readUnitById(int $id): ?Unit
    {
        
        $sql = "SELECT
        u.id AS unit_id,
        u.course_id AS unit_course_id,
        u.user_id AS unit_user_id,
        u.name AS unit_name,
        u.introduction AS unit_introduction,
        u.creation_date AS unit_creation_date,
        u.creation_time AS unit_creation_time,
        u.active AS unit_active,
        c.id AS course_id,
        c.name AS course_name,
        c.credits AS course_credits,
        us.id AS user_id,
        us.names AS user_names,
        us.last_names AS user_last_names,
        us.email AS user_email,
        us.date_of_birth AS user_date_of_birth,
        us.username AS user_username,
        us.password AS user_password,
        us.phone AS user_phone
        FROM units u
        INNER JOIN courses c ON u.course_id = c.id
        INNER JOIN users us ON u.user_id = us.id;
        WHERE u.id = :id";



        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $unit = new Unit();
            $unit->setId($row['unit_id']);
            $unit->setName($row['unit_name']);
            $unit->setIntroduction($row['unit_introduction']);
            $unit->setCreationDate($row['unit_creation_date']);
            $unit->setCreationTime($row['unit_creation_time']);
            $unit->setActive($row['unit_active']);

            // Crear objetos Course y User y establecer las relaciones
            $course = new Course();
            $course->setId($row['course_id']);
            $course->setName($row['course_name']);
            $course->setCredits($row['course_credits']);

            $user = new User();
            $user->setId($row['user_id']);
            $user->setNames($row['user_names']);
            $user->setLastNames($row['user_last_names']);
            $user->setEmail($row['user_email']);
            $user->setDateOfBirth($row['user_date_of_birth']);
            $user->setUsername($row['user_username']);
            $user->setPassword($row['user_password']);
            $user->setPhone($row['user_phone']);

            $unit->setCourse($course);
            $unit->setUser($user);

            return $unit;
        } else {
            return null;
        }
    }

    public function updateUnit(Unit $unit): ?Unit
    {
        $sql = "UPDATE units SET course_id = :course_id, user_id = :user_id, name = :name, 
                introduction = :introduction, creation_date = :creation_date, creation_time = :creation_time, active = :active WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        $id = $unit->getId();
        $courseId = $unit->getCourse()->getId();
        $userId = $unit->getUser()->getId();
        $name = $unit->getName();
        $introduction = $unit->getIntroduction();
        $creationDate = $unit->getCreationDate();
        $creationTime = $unit->getCreationTime();
        $active = $unit->isActive();

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':course_id', $courseId);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':introduction', $introduction);
        $stmt->bindParam(':creation_date', $creationDate);
        $stmt->bindParam(':creation_time', $creationTime);
        $stmt->bindParam(':active', $active);

        if ($stmt->execute()) {
            return $unit;
        }

        return null;
    }

    public function deleteUnit(int $id): ?Unit
    {
        $sql = "DELETE FROM units WHERE id = :id";
        $unit = $this->readUnitById($id);
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        
        if ($stmt->execute()) {
            return $unit;
        }
        return null;
    }

    public function allUnits(): array
    {
        $sql = "SELECT
        u.id AS unit_id,
        u.course_id AS unit_course_id,
        u.user_id AS unit_user_id,
        u.name AS unit_name,
        u.introduction AS unit_introduction,
        u.creation_date AS unit_creation_date,
        u.creation_time AS unit_creation_time,
        u.active AS unit_active,
        c.id AS course_id,
        c.name AS course_name,
        c.credits AS course_credits,
        us.id AS user_id,
        us.names AS user_names,
        us.last_names AS user_last_names,
        us.email AS user_email,
        us.date_of_birth AS user_date_of_birth,
        us.username AS user_username,
        us.password AS user_password,
        us.phone AS user_phone
        FROM units u
        INNER JOIN courses c ON u.course_id = c.id
        INNER JOIN users us ON u.user_id = us.id;
        ";

        $stmt = $this->pdo->query($sql);
        $units = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $unit = new Unit();
            $unit->setId($row['unit_id']);
            $unit->setName($row['unit_name']);
            $unit->setIntroduction($row['unit_introduction']);
            $unit->setCreationDate($row['unit_creation_date']);
            $unit->setCreationTime($row['unit_creation_time']);
            $unit->setActive($row['unit_active']);

            // Crear objetos Course y User y establecer las relaciones
            $course = new Course();
            $course->setId($row['course_id']);
            $course->setName($row['course_name']);
            $course->setCredits($row['course_credits']);

            $user = new User();
            $user->setId($row['user_id']);
            $user->setNames($row['user_names']);
            $user->setLastNames($row['user_last_names']);
            $user->setEmail($row['user_email']);
            $user->setDateOfBirth($row['user_date_of_birth']);
            $user->setUsername($row['user_username']);
            $user->setPassword($row['user_password']);
            $user->setPhone($row['user_phone']);

            $unit->setCourse($course);
            $unit->setUser($user);

            // Agregar el objeto Unit al array

            
            $units[] = $unit->getJsonWithRelations();
        }

        return $units;
    }
}
