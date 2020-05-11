<?php

namespace App\MessageHandler;

use App\Client\UrlGenerator;
use App\Client\WFClient;
use App\Message\Crop;
use App\Repository\BuildingRepository;
use App\Service\UpdateService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class CropHandler implements MessageHandlerInterface
{
    /**
     * @var BuildingRepository
     */
    private $buildingRepository;

    /**
     * @var UrlGenerator
     */
    private $urlGenerator;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var UpdateService
     */
    private $updateService;

    public function __construct(
        BuildingRepository $buildingRepository,
        UrlGenerator $urlGenerator,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        UpdateService $updateService
    ) {
        $this->buildingRepository = $buildingRepository;
        $this->urlGenerator = $urlGenerator;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->updateService = $updateService;
    }

    public function __invoke(Crop $message)
    {
        $building = $this->buildingRepository->find($message->getBuildingId());

        $client = new WFClient($building->getPlayer(), $this->urlGenerator, $this->entityManager);

        $response = $client->cropFarmland($building);

        if ($response === 'failed') {
            $this->logger->info('Reuse cookie failed. Relogin');
            $client->relogin($building->getPlayer());

            $response = $client->cropFarmland($building);
        }

        $response = json_decode($response, true);
        $this->logger->info('Farmland cropped - ID: ' . $building->getId());

        $this->updateService->update($response['updateblock'], $building->getPlayer(), $client);
        $this->logger->info('Updated');
    }
}
