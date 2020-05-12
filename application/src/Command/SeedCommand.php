<?php

namespace App\Command;

use App\Entity\Building;
use App\Entity\Product;
use App\Message\Crop;
use App\Message\Seed;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

class SeedCommand extends Command
{
    protected static $defaultName = 'app:dispatch:seed';

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
            ->setDescription('Seed something')
            ->addArgument('building', InputArgument::REQUIRED, 'Farmland ID')
            ->addArgument('product', InputArgument::REQUIRED, 'product ID')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->bus->dispatch(new Seed(
            (new Building())->setId($input->getArgument('building')),
            (new Product())->setId($input->getArgument('product')),
        ));
        return 0;
    }
}
