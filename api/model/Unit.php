<?php

namespace model;

require_once './model/User.php';

use model\User;

class Unit
{
    private int $id;
    private string $name;
    private string $introduction;
    private string $creationDate;
    private string $creationTime;
    private bool $active;
    private User $user;
    private Course $course;
    private array $activities = [];
    public static $rules = [
        'name' => 'required|string',
        'introduction' => 'required|string',
        'creationDate' => 'required|date',
        'creationTime' => 'required|time',
        'active' => 'required|boolean',
        "course_id" => 'required|numeric|exists:courses,id',
        "user_id" => 'required|numeric|exists:users,id'
    ];

    public function __construct()
    {
        $this->id = -1;
        $this->name = "";
        $this->introduction = "";
        $this->creationDate = "";
        $this->creationTime = "";
        $this->active = false;
        $this->user = new User();
        $this->course = new Course();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getIntroduction(): string
    {
        return $this->introduction;
    }

    public function setIntroduction(string $introduction)
    {
        $this->introduction = $introduction;
    }

    public function getCreationDate(): string
    {
        return $this->creationDate;
    }

    public function setCreationDate(string $creation_date)
    {
        $this->creationDate = $creation_date;
    }

    public function getCreationTime(): string
    {
        return $this->creationTime;
    }

    public function setCreationTime(string $creationTime)
    {
        $this->creationTime = $creationTime;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active)
    {
        $this->active = $active;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function getCourse()
    {
        return $this->course;
    }

    public function setCourse($course)
    {
        $this->course = $course;

        return $this;
    }

    public function getActivities()
    {
        return $this->activities;
    }

    public function setActivities($activity)
    {
        $this->activities[] = $activity;
    }


    public function getJson(): array
    {
        return array(
            'id' => $this->getId(),
            'name' => $this->getName(),
            'introduction' => $this->getIntroduction(),
            'creation_date' => $this->getCreationDate(),
            'creation_time' => $this->getCreationTime()
        );
    }

    public function getJsonWithRelations(): array
    {
        return array(
            'id' => $this->getId(),
            'name' => $this->getName(),
            'introduction' => $this->getIntroduction(),
            'creation_date' => $this->getCreationDate(),
            'creation_time' => $this->getCreationTime(),
            'course' => $this->getCourse()->getJson(),
            'user' => $this->getUser()->getJson(),
        );
    }


    public static function fillUnitFromRequestData(array $requestData): Unit
    {
        $unit = new Unit();


        if (isset($requestData['name'])) {
            $unit->setName($requestData['name']);
        }

        if (isset($requestData['introduction'])) {
            $unit->setIntroduction($requestData['introduction']);
        }

        if (isset($requestData['creationDate'])) {
            $unit->setCreationDate($requestData['creationDate']);
        }

        if (isset($requestData['creationTime'])) {
            $unit->setCreationTime($requestData['creationTime']);
        }

        if (isset($requestData['active'])) {
            $unit->setActive((bool) $requestData['active']);
        }

        if (isset($requestData['course_id'])) {
            $unit->getCourse()->setId((int) $requestData['course_id']);
        }

        if (isset($requestData['user_id'])) {
            $unit->getUser()->setId((int) $requestData['user_id']);
        }

        return $unit;
    }
}
