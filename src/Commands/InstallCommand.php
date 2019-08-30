<?php

namespace kranack\Lint\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
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
			->setHelp('This command install config for lint');
	}
	
	protected function isInstalled()
	{
		if (!Environment::isConfigured()) {
			throw new EnvironmentNotConfigured();
		}
	}

    protected function execute(InputInterface $input, OutputInterface $output)
    {
		try {
			$this->isInstalled();
		} catch (EnvironmentNotConfigured $e) {
			(new Environment())->init();
		}
    }
}