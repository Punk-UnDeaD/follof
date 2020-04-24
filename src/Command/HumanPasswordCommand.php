<?php

namespace App\Command;

use App\Model\User\Service\HumanStrongPasswordGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HumanPasswordCommand extends Command
{
    protected static $defaultName = 'app:human-password';

    private HumanStrongPasswordGenerator $passwordGenerator;

    public function __construct(HumanStrongPasswordGenerator $passwordGenerator)
    {
        $this->passwordGenerator = $passwordGenerator;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln($this->passwordGenerator->generate());

        return 0;
    }
}
