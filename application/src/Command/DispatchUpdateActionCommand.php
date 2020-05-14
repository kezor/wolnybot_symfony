<?php

namespace App\Command;

use App\Entity\Player;
use App\Message\Update;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class DispatchUpdateActionCommand extends Command
{
    protected static $defaultName = 'app:dispatch:update';
    /**
     * @var MessageBusInterface
     */
    private $bus;

    public function __construct(MessageBusInterface $bus, string $name = null)
    {
        parent::__construct($name);
        $this->bus = $bus;
    }

    protected function configure()
    {
        $this
            ->setDescription('Update data for Player')
            ->addArgument('player', InputArgument::REQUIRED, 'Player ID')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->bus->dispatch(new Update((new Player())->setId($input->getArgument('player'))));
        return 0;
    }
}
