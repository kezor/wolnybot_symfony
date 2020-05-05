<?php

namespace App\MessageHandler;

use App\Client\UrlGenerator;
use App\Client\WFClient;
use App\Message\Update;
use App\Repository\PlayerRepository;
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

    public function __construct(PlayerRepository $playerRepository, UrlGenerator $urlGenerator)
    {
        $this->playerRepository = $playerRepository;
        $this->urlGenerator = $urlGenerator;
    }

    public function __invoke(Update $message)
    {
        $player = $this->playerRepository->find($message->getPlayerId());

        $client = new WFClient($player, $this->urlGenerator);

        var_dump($client->getDashboardData());
    }
}
