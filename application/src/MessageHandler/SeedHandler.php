<?php

namespace App\MessageHandler;

use App\Client\UrlGenerator;
use App\Client\WFClient;
use App\Message\Crop;
use App\Message\Seed;
use App\Repository\BuildingRepository;
use App\Repository\ProductRepository;
use App\Service\FarmlandService;
use App\Service\UpdateService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

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

    public function __construct(
        BuildingRepository $buildingRepository,
        UrlGenerator $urlGenerator,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        UpdateService $updateService,
        ProductRepository $productRepository,
        FarmlandService $farmlandService
    ) {
        $this->buildingRepository = $buildingRepository;
        $this->urlGenerator = $urlGenerator;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->updateService = $updateService;
        $this->productRepository = $productRepository;
        $this->farmlandService = $farmlandService;
    }

    public function __invoke(Seed $message)
    {
        $building = $this->buildingRepository->find($message->getBuildingId());
        $product = $this->productRepository->find($message->getProductId());

        $client = new WFClient($building->getPlayer(), $this->urlGenerator, $this->entityManager);

        $response = $this->farmlandService->seed($client, $building, $product);

        dd($response);
        $this->updateService->update($response, $building->getPlayer(), $client);
    }
}
