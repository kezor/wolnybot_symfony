<?php

namespace App\Message;

use App\Entity\Building;
use App\Entity\Product;

final class Seed
{
    private $buildingId;

    private $productId;

    public function __construct(Building $building, Product $product)
    {
        $this->buildingId = $building->getId();
        $this->productId = $product->getId();
    }

    public function getBuildingId(): string
    {
        return $this->buildingId;
    }

    public function getProductId(): string
    {
        return $this->productId;
    }
}
