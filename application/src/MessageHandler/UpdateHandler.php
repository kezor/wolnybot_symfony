<?php

namespace App\MessageHandler;

use App\Client\UrlGenerator;
use App\Client\WFClient;
use App\Entity\Building;
use App\Entity\Farm;
use App\Entity\Player;
use App\Entity\Product;
use App\Message\Update;
use App\Repository\BuildingRepository;
use App\Repository\FarmRepository;
use App\Repository\PlayerRepository;
use App\Repository\ProductRepository;
use App\Service\FarmlandService;
use App\Type\BuildingType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class UpdateHandler implements MessageHandlerInterface
{
    /**
     * @var PlayerRepository
     */
    private $playerRepository;

    /**
     * @var UrlGenerator
     */
    private $urlGenerator;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var ProductRepository
     */
    private $productRepository;
    /**
     * @var FarmRepository
     */
    private $farmRepository;
    /**
     * @var BuildingRepository
     */
    private $buildingRepository;

    /**
     * @var WFClient
     */
    private $client;
    /**
     * @var FarmlandService
     */
    private $farmlandService;

    public function __construct(
        PlayerRepository $playerRepository,
        UrlGenerator $urlGenerator,
        EntityManagerInterface $entityManager,
        ProductRepository $productRepository,
        FarmRepository $farmRepository,
    BuildingRepository $buildingRepository,
    FarmlandService $farmlandService
    ) {
        $this->playerRepository = $playerRepository;
        $this->urlGenerator = $urlGenerator;
        $this->entityManager = $entityManager;
        $this->productRepository = $productRepository;
        $this->farmRepository = $farmRepository;
        $this->buildingRepository = $buildingRepository;
        $this->farmlandService = $farmlandService;
    }

    public function __invoke(Update $message)
    {
        $player = $this->playerRepository->find($message->getPlayerId());

        $this->client = new WFClient($player, $this->urlGenerator, $this->entityManager);

        $response = $this->client->getDashboardData();

        if ($response === 'failed') {
            $this->client->relogin($player);
        }

        $response = $this->client->getDashboardData();

        $response = json_decode($response, true);

        $stocks = $response['updateblock']['stock']['stock'];
        $this->updateStock($stocks, $player);
        $farms = $response['updateblock']['farms']['farms'];
        $this->updateFarms($farms, $player);

        $this->entityManager->flush();
    }

    private function updateStock(array $stocks, Player $player)
    {
        foreach ($stocks as $product) {
            foreach ($product as $level1) {
                foreach ($level1 as $level2) {
                    $product = $this->getProduct($level2, $player);
                    $product->setAmount($level2['amount']);
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

    private function updateFarms($farms, Player $player)
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
                            $this->updateFields($building);
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
        $farm = $this->farmRepository->findOneBy(['player' => $player, 'farmIndex'  => $farmId]);
        if($farm === null){
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
            'farm' => $farm,
            'player' => $player,
            'position' => $spaceData['position']
        ]);

        if($building === null){
            $building = new Building();
            $building->setFarm($farm)
                ->setPosition($spaceData['position'])
                ->setType($spaceData['buildingid'])
                ->setPlayer($player)
            ;
            $this->entityManager->persist($building);
        }
        return $building;
    }

    private function updateFields(Building $building)
    {
        $fieldsData = $this->client->getFarmlandFields($building);

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
//                $updatedFieldIndexes[] = $fieldData['teil_nr'];
            }
        }
//        $farmland->clearFields($updatedFieldIndexes);
    }
}
