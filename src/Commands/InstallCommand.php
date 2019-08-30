<?php

namespace kranack\Lint\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use kranack\Lint\Env\Environment;
use kranack\Lint\Exceptions\EnvironmentNotConfigured;

class InstallCommand extends Command
{
	protected static $defaultName = 'install';

    protected function configure()
    {
		$this
			->setDescription('Install config')
			->setHelp('This command install config for lint')
			->addOption('force', 'f', InputOption::VALUE_NONE, 'Force install');
	}
	
	protected function isInstalled()
	{
		if (!Environment::isConfigured()) {
			throw new EnvironmentNotConfigured();
		}
	}

	protected function install()
	{
		(new Environment())->init();
	}

    protected function execute(InputInterface $input, OutputInterface $output)
    {
		$force = $input->getOption('force');

		try {
			$this->isInstalled();

			if ($force) { $this->install(); }
		} catch (EnvironmentNotConfigured $e) {
			$this->install();
		}
    }
}