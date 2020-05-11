<?php

namespace App\Message;

use App\Entity\Building;

final class Crop
{
    private $buildingId;

    public function __construct(Building $building)
    {
        $this->buildingId = $building->getId();
    }

    public function getBuildingId(): string
    {
        return $this->buildingId;
    }
}
