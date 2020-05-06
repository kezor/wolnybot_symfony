<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FieldRepository")
 */
class Field
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Building")
     * @ORM\JoinColumn(nullable=false)
     */
    private $building;

    /**
     * @ORM\Column(type="integer")
     */
    private $offsetX;

    /**
     * @ORM\Column(type="integer")
     */
    private $offsetY;

    /**
     * @ORM\Column(type="integer")
     */
    private $phase;

    /**
     * @ORM\Column(type="boolean")
     */
    private $planted;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBuilding(): ?Building
    {
        return $this->building;
    }

    public function setBuilding(?Building $building): self
    {
        $this->building = $building;

        return $this;
    }

    public function getOffsetX(): ?int
    {
        return $this->offsetX;
    }

    public function setOffsetX(int $offsetX): self
    {
        $this->offsetX = $offsetX;

        return $this;
    }

    public function getOffsetY(): ?int
    {
        return $this->offsetY;
    }

    public function setOffsetY(int $offsetY): self
    {
        $this->offsetY = $offsetY;

        return $this;
    }

    public function getPhase(): ?int
    {
        return $this->phase;
    }

    public function setPhase(int $phase): self
    {
        $this->phase = $phase;

        return $this;
    }

    public function getPlanted(): ?bool
    {
        return $this->planted;
    }

    public function setPlanted(bool $planted): self
    {
        $this->planted = $planted;

        return $this;
    }
}
