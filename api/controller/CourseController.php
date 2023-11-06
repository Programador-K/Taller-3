<?php

namespace controller;

require_once 'controller/BaseController.php';
require_once 'model/Course.php';
require_once 'util/JsonResponse.php';
require_once 'dao/impl/CourseMySqlDao.php';
require_once 'dao/CourseDao.php';
require_once 'validation/Request.php';

use validation\Request;
use controller\BaseController;
use dao\impl\CourseMySqlDao;
use dao\CourseDao;
use model\Course;
use util\JsonResponse;

class CourseController extends BaseController
{

    private CourseDao $courseDao;

    public function __construct()
    {

        $this->courseDao = new CourseMySqlDao();
    }

    public function all()
    {
        $courses = $this->courseDao->allCourses();
        JsonResponse::send(200, 'Listado de cursos', $courses, 'COURSES_GET_OK', 200);
    }

    public function create()
    {
        $requestData = json_decode(file_get_contents('php://input'), true);

        Request::validate($requestData, Course::$rules);

        $course = Course::fillCourseFromRequestData($requestData);

        $courseCreated = $this->courseDao->createCourse($course);

        if ($courseCreated != null) {
            JsonResponse::send(200, 'Registro satisfactorio', [$courseCreated->getJson()], 'COURSE_INSERT_OK', 201);
        } else {
            JsonResponse::send(500, 'Registro erroneo', [], 'COURSE_INSERT_ERROR', 500);
        }
    }

    public function readById()
    {
        $id = $_GET['id'] ?? null;

        $this->validateIdParameter($id);

        $courseId = (int) $id;

        $course = $this->courseDao->readCourseById($courseId);

        if ($course) {
            JsonResponse::send(200, 'Busqueda satisfactoria', $course->getJson(), 'COURSE_GET_OK', 200);
        } else {
            JsonResponse::send(404, 'Curso no encontrado', [], 'COURSE_GET_ERROR', 404);
        }
    }


    public function update()
    {

        $id = $_GET['id'] ?? null;

        $this->validateIdParameter($id);

        $requestData = json_decode(file_get_contents('php://input'), true);
        
        Request::validate($requestData, Course::$rules);

        $course = Course::fillCourseFromRequestData($requestData);

        $course->setId($id);

        $courseUpdated = $this->courseDao->updateCourse($course);

        if ($courseUpdated != null) {
            JsonResponse::send(200, 'Curso actualizado exitosamente', [$courseUpdated->getJson()], 'COURSE_UPDATE_OK', 200);
        } else {
            JsonResponse::send(500, 'Error al actualizar el curso', [], 'COURSE_UPDATE_ERROR', 500);
        }
    }

    public function delete()
    {
        $id = $_GET['id'] ?? null;

        $this->validateIdParameter($id);

        $courseDeleted = $this->courseDao->deleteCourse($id);

        if ($courseDeleted) {
            JsonResponse::send(200, 'Curso eliminado exitosamente', [$courseDeleted->getJson()], 'COURSE_DELETE_OK', 200);
        } else {
            JsonResponse::send(301, 'Error al eliminar el curso', [], 'COURSE_DELETE_ERROR', 500);
        }
    }
}
