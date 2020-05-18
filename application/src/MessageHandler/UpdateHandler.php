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
use App\Service\UpdateService;
use App\Type\BuildingType;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
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
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var UpdateService
     */
    private $updateService;

    public function __construct(
        PlayerRepository $playerRepository,
        UrlGenerator $urlGenerator,
        EntityManagerInterface $entityManager,
        ProductRepository $productRepository,
        FarmRepository $farmRepository,
    BuildingRepository $buildingRepository,
    FarmlandService $farmlandService,
    LoggerInterface $logger,
    UpdateService $updateService
    ) {
        $this->playerRepository = $playerRepository;
        $this->urlGenerator = $urlGenerator;
        $this->entityManager = $entityManager;
        $this->productRepository = $productRepository;
        $this->farmRepository = $farmRepository;
        $this->buildingRepository = $buildingRepository;
        $this->farmlandService = $farmlandService;
        $this->logger = $logger;
        $this->updateService = $updateService;
    }

    public function __invoke(Update $message)
    {
        $player = $this->playerRepository->find($message->getPlayerId());

        $this->client = new WFClient($player, $this->urlGenerator, $this->entityManager);

        $this->logger->info('Trying to use stored cookies');
        $response = $this->client->getDashboardData();

        if ($response === 'failed') {
            $this->logger->info('Reuse cookie failed. Relogin');
            $this->client->relogin($player);

            $response = $this->client->getDashboardData();
        }

        $responseData = json_decode($response, true);

        if($responseData === null){
            $this->logger->debug('INVALID JSON: '. $response);

        }
        $this->updateService->update($responseData, $player, $this->client);
    }
}
