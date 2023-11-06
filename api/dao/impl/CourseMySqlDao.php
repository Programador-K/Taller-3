<?php

namespace dao\impl;

require_once 'model/Course.php';
require_once 'dao/CourseDao.php';
require_once 'util/DatabaseConnection.php';

use \util\DatabaseConnection;
use \model\Course;
use \dao\CourseDao;
use \PDO;

class CourseMySqlDao implements CourseDao
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = DatabaseConnection::getInstance()->getConnection();
    }

    public function createCourse(Course $course): Course
    {
        $sql = "INSERT INTO courses (name, credits) VALUES (:name, :credits)";

        $stmt = $this->pdo->prepare($sql);

        // Guarda los valores en variables
        $name = $course->getName();
        $credits = $course->getCredits();

        // Utiliza las variables en bindParam
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':credits', $credits);;


        if ($stmt->execute()) {

            $lastInsertId = $this->pdo->lastInsertId();
            $course->setId($lastInsertId);

            return $course;
        } else {
            return null;
        }
    }

    public function readCourseById(int $id): ?Course
    {
        $sql = "SELECT * FROM courses WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $course = new Course();
            $course->setId($row['id']);
            $course->setName($row['name']);
            $course->setCredits((int)$row['credits']);

            return $course;
        } else {
            return null;
        }
    }

    public function updateCourse(Course $course): ?Course
    {
        $sql = "UPDATE courses SET name = :name, credits = :credits WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        // Guarda los valores en variables
        $id = $course->getId();
        $name = $course->getName();
        $credits = $course->getCredits();

        // Utiliza las variables en bindParam
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':credits', $credits);
        
        if ($stmt->execute()) {
            return $course;
        } else {
            return null;
        }

    }

    public function deleteCourse(int $id): Course
    {
        $sql = "DELETE FROM courses WHERE id = :id";
        $course = $this->readCourseById($id);
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        if ($stmt->execute()) {
            return $course;
        } else {
            return null;
        }
    }

    public function allCourses(): array
    {
        $sql = "SELECT * FROM courses";
        $stmt = $this->pdo->query($sql);
        $courses = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $course = new Course();
            $course->setId((int)$row['id']);
            $course->setName($row['name']);
            $course->setCredits((int)$row['credits']);

            $courses[] = $course->getJson();
        }

        return $courses;
    }
}
