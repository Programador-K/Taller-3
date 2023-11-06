<?php

namespace model;

class Activity
{
    private int $id;
    private string $title;
    private string $description;
    private string $activitiescol;
    private Unit $unit;
    public static $rules = [
        'title' => 'required|string',
        'description' => 'required|string',
        'activitiescol' => 'required|string',
        'unit_id' => 'required|numeric|exists:units,id',
    ];

    public function __construct()
    {
        $this->id = -1;
        $this->title = "";
        $this->description = "";
        $this->activitiescol = "";
        $this->unit = new Unit();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function getUnit(): Unit
    {
        return $this->unit;
    }

    public function setUnitId(Unit $unit)
    {
        $this->unit = $unit;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    public function getActivitiesCol(): string
    {
        return $this->activitiescol;
    }

    public function setActivitiesCol(string $activitiescol)
    {
        $this->activitiescol = $activitiescol;
    }


    public function getJson(): array
    {
        return array(
            'id' => $this->getId(),
            'unit_id' => $this->getUnit()->getId(),
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'activitiescol' => $this->getActivitiesCol()
        );
    }

    public function getJsonWithRelations(): array
    {
        return array(
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'activitiescol' => $this->getActivitiesCol(),
            'unit' => $this->getUnit()->getJson(),
        );
    }



    public static function fillActivityFromRequestData(array $requestData): Activity
    {
        $activity = new Activity();

        if (isset($requestData['id'])) {
            $activity->setId((int) $requestData['id']);
        }

        if (isset($requestData['unit_id'])) {
            $activity->getUnit()->setId((int) $requestData['unit_id']);
        }

        if (isset($requestData['title'])) {
            $activity->setTitle($requestData['title']);
        }

        if (isset($requestData['description'])) {
            $activity->setDescription($requestData['description']);
        }

        if (isset($requestData['activitiescol'])) {
            $activity->setActivitiescol($requestData['activitiescol']);
        }

        return $activity;
    }

   
    public function setUnit($unit)
    {
        $this->unit = $unit;

        return $this;
    }
}
