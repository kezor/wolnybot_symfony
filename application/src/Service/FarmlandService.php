<?php declare(strict_types=1);


namespace App\Service;


use App\Client\WFClient;
use App\Entity\Building;
use App\Entity\Field;
use App\Entity\Product;
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
        $field = $this->getField($fieldData, $building);

        $field->setProduct($this->productRepository->findOneBy(['pid' => $fieldData['inhalt']??null]))
            ->setPhase($fieldData['phase'])
            ->setPlanted($fieldData['gepflanzt']??'');
//        $field->product_pid = $fieldData['inhalt'];
//        $field->offset_x    = $fieldData['x'];
//        $field->offset_y    = $fieldData['y'];
//        $field->phase       = $fieldData['phase'];
//        $field->planted     = $fieldData['gepflanzt'];
//        $field->time        = $fieldData['zeit'];
//        $field->water       = (bool)$fieldData['iswater'];
    }

    private function getField(array $fieldData, Building $building): Field
    {
//        dd($fieldData);
        $field = $this->fieldRepository->findOneBy([
            'building' => $building,
            'position' => $fieldData['teil_nr'],

        ]);

        if ($field === null) {
            $field = new Field();
            $field->setBuilding($building)
                ->setOffsetX($fieldData['x']??null)
                ->setOffsetY($fieldData['y']??null)
                ->setPosition($fieldData['teil_nr']);
            $this->entityManager->persist($field);
        }

        return $field;
    }

    public function clearOtherFields(array $updatedFieldIndexes, Building $building)
    {
        $keysToClear = array_diff(array_keys(array_fill(1, 120, 'x')), array_keys($updatedFieldIndexes));

        foreach ($keysToClear as $position){
            $this->updateField($building, ['teil_nr' => $position, 'phase' => 0, 'gepflanzt' => null]);
        }
    }

    public function seed(
        WFClient $client,
        ?Building $building,
        ?Product $product
    )
    {
        $emptyFields = $this->fieldRepository->findEmptyFields($building);

        foreach ($emptyFields as $field){
            $response = $client->seed($building, $field, $product);
        }

        return $response;
    }
}