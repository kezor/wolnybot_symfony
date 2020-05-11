<?php

namespace App\Command;

use App\Entity\Building;
use App\Message\Crop;
use App\Message\Update;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class DispatchCropActionCommand extends Command
{
    protected static $defaultName = 'app:dispatch:crop';

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
            ->setDescription('Crop all plants at farmland (builiding) ID')
            ->addArgument('building', InputArgument::REQUIRED, 'Farmland ID')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->bus->dispatch(new Crop((new Building())->setId($input->getArgument('building'))));
        return 0;
    }
}
