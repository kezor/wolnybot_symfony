<?php

namespace App\MessageHandler;

use App\Client\UrlGenerator;
use App\Client\WFClient;
use App\Message\Update;
use App\Repository\PlayerRepository;
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

    public function __construct(PlayerRepository $playerRepository, UrlGenerator $urlGenerator, EntityManagerInterface $entityManager)
    {
        $this->playerRepository = $playerRepository;
        $this->urlGenerator = $urlGenerator;
        $this->entityManager = $entityManager;
    }

    public function __invoke(Update $message)
    {
        $player = $this->playerRepository->find($message->getPlayerId());

        $client = new WFClient($player, $this->urlGenerator, $this->entityManager);

        var_dump($client->getDashboardData());
    }
}
