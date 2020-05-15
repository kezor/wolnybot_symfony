<?php

namespace App\MessageHandler;

use App\Client\UrlGenerator;
use App\Client\WFClient;
use App\Entity\Product;
use App\Message\Crop;
use App\Message\Seed;
use App\Repository\BuildingRepository;
use App\Repository\ProductRepository;
use App\Repository\TaskRepository;
use App\Service\FarmlandService;
use App\Service\UpdateService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

final class SeedHandler implements MessageHandlerInterface
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
     * @var ProductRepository
     */
    private $productRepository;
    /**
     * @var FarmlandService
     */
    private $farmlandService;
    /**
     * @var TaskRepository
     */
    private $taskRepository;
    /**
     * @var MessageBusInterface
     */
    private $bus;

    public function __construct(
        BuildingRepository $buildingRepository,
        UrlGenerator $urlGenerator,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        UpdateService $updateService,
        ProductRepository $productRepository,
        FarmlandService $farmlandService,
        TaskRepository $taskRepository,
        MessageBusInterface $bus
    ) {
        $this->buildingRepository = $buildingRepository;
        $this->urlGenerator = $urlGenerator;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->updateService = $updateService;
        $this->productRepository = $productRepository;
        $this->farmlandService = $farmlandService;
        $this->taskRepository = $taskRepository;
        $this->bus = $bus;
    }

    public function __invoke(Seed $message)
    {
        $building = $this->buildingRepository->find($message->getBuildingId());
        $product = $this->productRepository->find($message->getProductId());

        $client = new WFClient($building->getPlayer(), $this->urlGenerator, $this->entityManager);

        $response = $this->farmlandService->seed($client, $building, $product);

        $this->logger->info('Seeded');

        if (strpos($response, 'Błąd bazy danych' !== false)) {
            $this->bus->dispatch($message, [new DelayStamp(60 * 60 * 1000)]);
            $this->logger->info('Message Delayed 1h');
        }

        if (!$response) {
            $this->logger->info('Nothing has beed seeded - no empty fields');

            return;
        }
        $response = json_decode($response, true);
        $this->updateService->update($response, $building->getPlayer(), $client);
        $this->logger->info('Updated');

        $this->bus->dispatch(new Crop($building), [new DelayStamp($this->getDelay($product))]); // delay 16-23 minutes
    }

    private function getDelay(Product $product): int
    {
        $minCropTriggeredTime = $product->getGrowingTime();
        $maxCropTriggeredTime = $minCropTriggeredTime + random_int(34, 200);

        return random_int($minCropTriggeredTime, $maxCropTriggeredTime) * 1000;
    }
}
