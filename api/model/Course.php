<?php

namespace model;

class Course
{
    private int $id;
    private string $name;
    private int $credits;
    private array $units = [];
    public static $rules = [
        'name' => 'required|string',
        'credits' => 'required|numeric',
    ];

    public function __construct()
    {
        $this->id = -1;
        $this->name = "";
        $this->credits = -1;
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

    public function getCredits(): int
    {
        return $this->credits;
    }

    public function setCredits(int $credits)
    {
        $this->credits = $credits;
    }

    public function getUnits(): array
    {
        return $this->units;
    }

    public function setUnits(array $units)
    {
        $this->units = $units;
    }

    public function addUnit(Unit $unit)
    {
        $this->units[] = $unit;
    }

    public function getJson(): array
    {
        return array(
            'id' => $this->getId(),
            'name' => $this->getName(),
            'credits' => $this->getCredits()
        );
    }

    public function getJsonWithRelations(): array
    {
        $unitsData = [];

        foreach ($this->units as $unit) {
            $unitsData[] = $unit->getJsonWithRelations();
        }

        return array(
            'id' => $this->getId(),
            'name' => $this->getName(),
            'credits' => $this->getCredits(),
            'units' => $unitsData
        );
    }

    public static function fillCourseFromRequestData(array $requestData): Course
    {
        $course = new Course();

        if (isset($requestData['id'])) {
            $course->setId((int)$requestData['id']);
        }

        if (isset($requestData['name'])) {
            $course->setName($requestData['name']);
        }

        if (isset($requestData['credits'])) {
            $course->setCredits((int) $requestData['credits']);
        }

        return $course;
    }
}
