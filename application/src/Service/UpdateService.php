<?php declare(strict_types=1);


namespace App\Service;


use App\Client\WFClient;
use App\Entity\Building;
use App\Entity\Farm;
use App\Entity\Player;
use App\Entity\Product;
use App\Repository\BuildingRepository;
use App\Repository\FarmRepository;
use App\Repository\ProductRepository;
use App\Type\BuildingType;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class UpdateService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var FarmRepository
     */
    private $farmRepository;

    /**
     * @var BuildingRepository
     */
    private $buildingRepository;

    /**
     * @var FarmlandService
     */
    private $farmlandService;

    public function __construct(
        EntityManagerInterface $entityManager,
        ProductRepository $productRepository,
        LoggerInterface $logger,
        FarmRepository $farmRepository,
        BuildingRepository $buildingRepository,
        FarmlandService $farmlandService
    ) {
        $this->entityManager = $entityManager;
        $this->productRepository = $productRepository;
        $this->logger = $logger;
        $this->farmRepository = $farmRepository;
        $this->buildingRepository = $buildingRepository;
        $this->farmlandService = $farmlandService;
    }

    public function update(array $updateblock, Player $player, WFClient $client)
    {
        $stocks = $updateblock['stock']['stock'];
        $this->updateStock($stocks, $player);
        $this->logger->info('Store updated');
        $farms = $updateblock['farms']['farms'];
        $this->updateFarms($farms, $player, $client);
        $this->logger->info('Farms updated');
        $this->entityManager->flush();
    }

    private function updateStock(array $stocks, Player $player)
    {
        foreach ($stocks as $product) {
            foreach ($product as $level1) {
                foreach ($level1 as $level2) {
                    $product = $this->getProduct($level2, $player);
                    $product->setAmount((int)$level2['amount']);
                }
            }
        }
    }

    private function getProduct($level2, Player $player): Product
    {
        $product = $this->productRepository->findOneBy(['player' => $player, 'pid' => $level2['pid']]);

        if ($product === null) {
            $product = new Product();
            $product->setPid($level2['pid']);
            $product->setPlayer($player);
            $this->entityManager->persist($product);
        }

        return $product;
    }

    private function updateFarms($farms, Player $player, WFClient $client)
    {
        foreach ($farms as $farmId => $farmData) {
            $farm = $this->getFarm($farmId, $player);
            foreach ($farmData as $spaceData) {
                if ($spaceData['status'] == 1) {
                    switch ($spaceData['buildingid']) {
                        case BuildingType::FARMLAND:
//                            var_dump('Updating....');
                            $building = $this->getBuilding($farm, $player, $spaceData);

//                            $farmland->fillInFields();
                            $this->updateFields($building, $client);
//                            $farmland->push();
                            break;
//                        case BuildingType::HOVEL:
//                            $this->processHovel($spaceData, $farmId);
//                            break;
                    }
//                    $this->usedSeeds = []; // reset used products for new space}
                }
            }
        }
    }

    private function getFarm(int $farmId, Player $player): Farm
    {
        $farm = $this->farmRepository->findOneBy(['player' => $player, 'farmIndex' => $farmId]);
        if ($farm === null) {
            $farm = new Farm();
            $farm->setFarmIndex($farmId);
            $farm->setPlayer($player);
            $this->entityManager->persist($farm);
        }

        return $farm;
    }

    private function getBuilding(Farm $farm, Player $player, $spaceData): Building
    {
        $building = $this->buildingRepository->findOneBy([
            'farm'     => $farm,
            'player'   => $player,
            'position' => $spaceData['position'],
        ]);

        if ($building === null) {
            $building = new Building();
            $building->setFarm($farm)
                ->setPosition($spaceData['position'])
                ->setType($spaceData['buildingid'])
                ->setPlayer($player);
            $this->entityManager->persist($building);
        }

        return $building;
    }

    private function updateFields(Building $building, WFClient $client)
    {
        $fieldsData = $client->getFarmlandFields($building);

        $fieldsData = json_decode($fieldsData, true);
//        dd($fieldsData);
        $fields = $fieldsData['datablock'][1];

        $updatedFieldIndexes = [];

        if ($fields != 0) {
            foreach ($fields as $key => $fieldData) {
                if (!is_numeric($key)) {
                    continue;
                }
                $this->farmlandService->updateField($building, $fieldData);
//                $farmland->updateField($fieldData);
                $updatedFieldIndexes[$fieldData['teil_nr']] = $fieldData['teil_nr'];
            }
        }
        $this->farmlandService->clearOtherFields($updatedFieldIndexes, $building);
    }

}