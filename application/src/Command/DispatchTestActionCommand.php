<?php

namespace App\Command;

use App\Entity\Player;
use App\Message\Update;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class DispatchTestActionCommand extends Command
{
    protected static $defaultName = 'app:dispatch-test-action';
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
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->bus->dispatch(new Update((new Player())->setId(1)));
        return 0;
    }
}
