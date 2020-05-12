<?php

namespace App\Command;

use App\Entity\Building;
use App\Entity\Product;
use App\Entity\Task;
use App\Repository\BuildingRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateTaskCommand extends Command
{
    protected static $defaultName = 'app:create:task';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var BuildingRepository
     */
    private $buildingRepository;
    /**
     * @var ProductRepository
     */
    private $productRepository;

    public function __construct(
        string $name = null,
        EntityManagerInterface $entityManager,
        BuildingRepository $buildingRepository,
        ProductRepository $productRepository
    )
    {
        parent::__construct($name);
        $this->entityManager = $entityManager;
        $this->buildingRepository = $buildingRepository;
        $this->productRepository = $productRepository;
    }

    protected function configure()
    {
        $this
            ->setDescription('Create Task for building')
            ->addArgument('building', InputArgument::REQUIRED, 'Farmland ID')
            ->addArgument('product', InputArgument::REQUIRED, 'Product ID');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $task = new Task();
        $task->setStatus(1)
            ->setBuilding($this->buildingRepository->find($input->getArgument('building')))
            ->setProduct($this->productRepository->find($input->getArgument('product')))
        ;

        $this->entityManager->persist($task);
        $this->entityManager->flush();

        $io = new SymfonyStyle($input, $output);
        $io->success('New task has been created');

        return 0;
    }
}
