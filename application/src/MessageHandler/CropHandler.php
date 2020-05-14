<?php

namespace App\MessageHandler;

use App\Client\UrlGenerator;
use App\Client\WFClient;
use App\Message\Crop;
use App\Message\Seed;
use App\Repository\BuildingRepository;
use App\Repository\TaskRepository;
use App\Service\UpdateService;
use Doctrine\Migrations\Configuration\Exception\JsonNotValid;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

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
    /**
     * @var TaskRepository
     */
    private $taskRepository;
    /**
     * @var MessageBus
     */
    private $bus;

    public function __construct(
        BuildingRepository $buildingRepository,
        UrlGenerator $urlGenerator,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        UpdateService $updateService,
        TaskRepository $taskRepository,
        MessageBusInterface $bus
    ) {
        $this->buildingRepository = $buildingRepository;
        $this->urlGenerator = $urlGenerator;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->updateService = $updateService;
        $this->taskRepository = $taskRepository;
        $this->bus = $bus;
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

        $responseData = json_decode($response, true);

        if($responseData === null){
            $this->logger->error('JSON invalid' . $response);
            throw new JsonNotValid($response);
        }

        $this->logger->info('Farmland cropped - ID: ' . $building->getId());

        $this->updateService->update($responseData, $building->getPlayer(), $client);
        $this->logger->info('Updated');

        $task = $this->taskRepository->findOneBy(['building' => $building, 'status' => 1]);

        if ($task) {
            $this->bus->dispatch(new Seed($building, $task->getProduct()));
        }
    }
}
