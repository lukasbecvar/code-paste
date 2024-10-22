<?php

namespace App\Command;

use App\Util\AppUtil;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ToggleMaintenanceCommand
 *
 * The command for enable/disable maintenance mode
 *
 * @package App\Command
 */
#[AsCommand(name: 'app:toggle:maintenance', description: 'Enable/disable maintenance mode')]
class ToggleMaintenanceCommand extends Command
{
    private AppUtil $appUtil;

    public function __construct(AppUtil $appUtil)
    {
        $this->appUtil = $appUtil;
        parent::__construct();
    }

    /**
     * Execute the maintenance mode toggle command
     *
     * @param InputInterface $input The input interface
     * @param OutputInterface $output The output interface
     *
     * @return int The command exit code
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            // get current mode
            $mode = $this->appUtil->getEnvValue('MAINTENANCE_MODE');

            // set new mode
            if ($mode === 'true') {
                $newMode = 'false';
            } else {
                $newMode = 'true';
            }

            // update mode value
            $this->appUtil->updateEnvValue('MAINTENANCE_MODE', $newMode);

            // return success status
            $io->success("MAINTENANCE_MODE in .env has been set to true");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('Error to toggle maintenance mode: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
