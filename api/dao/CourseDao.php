<?php

namespace dao;

require_once './model/Course.php';

use \model\Course;

interface CourseDao
{
    public function createCourse(Course $course): ?Course;
    public function readCourseById(int $id): ?Course;
    public function updateCourse(Course $course): ?Course;
    public function deleteCourse(int $id): ?Course;
    public function allCourses(): array;
}
