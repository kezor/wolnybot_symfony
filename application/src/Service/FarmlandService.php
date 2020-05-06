<?php declare(strict_types=1);


namespace App\Service;


use App\Entity\Building;
use App\Entity\Field;
use App\Repository\FieldRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;

class FarmlandService
{
    /**
     * @var FieldRepository
     */
    private $fieldRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var ProductRepository
     */
    private $productRepository;

    public function __construct(
        FieldRepository $fieldRepository,
        EntityManagerInterface $entityManager,
        ProductRepository $productRepository
    ) {
        $this->fieldRepository = $fieldRepository;
        $this->entityManager = $entityManager;
        $this->productRepository = $productRepository;
    }

    public function updateField(Building $building, $fieldData)
    {
        $field = $this->getField((int)$fieldData['teil_nr'], $building);

        $field->setProduct($this->productRepository->findOneBy(['pid' => $fieldData['inhalt']]))
            ->setOffsetX($fieldData['x'])
            ->setOffsetY($fieldData['y'])
            ->setPhase($fieldData['phase'])
            ->setPlanted($fieldData['gepflanzt']);
//        $field->product_pid = $fieldData['inhalt'];
//        $field->offset_x    = $fieldData['x'];
//        $field->offset_y    = $fieldData['y'];
//        $field->phase       = $fieldData['phase'];
//        $field->planted     = $fieldData['gepflanzt'];
//        $field->time        = $fieldData['zeit'];
//        $field->water       = (bool)$fieldData['iswater'];
    }

    private function getField(int $teil_nr, Building $building): Field
    {
        $field = $this->fieldRepository->findOneBy([
            'building' => $building,
            'position' => $teil_nr,
        ]);
        if ($field === null) {
            $field = new Field();
            $field->setBuilding($building)
                ->setPosition($teil_nr);
            $this->entityManager->persist($field);
        }

        return $field;
    }
}