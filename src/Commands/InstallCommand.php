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
			->addOption('list', 'l', InputOption::VALUE_NONE, 'List cached PHP instances')
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

	protected function list(OutputInterface $output)
	{
		$list = [];

		if (Environment::isConfigured()) {
			$list = Environment::getConfig()->get('paths', []);
		} else {
			$output->writeln('<fg=red;options=bold>No configuration found</>');
		}
		
		$total = count($list);
		$count = 0;

		foreach ($list as $path) {
			$output->writeln(sprintf('<fg=cyan;options=bold>Path</> %s', ($path->path ?? '-')));
			$output->writeln(sprintf('<fg=cyan;options=bold>Type</> %s', ($path->type ?? '-')));

			if (++$count !== $total) {
				$output->writeln('----------------------------');
			}
		}
	}

    protected function execute(InputInterface $input, OutputInterface $output)
    {
		if ($input->getOption('list')) {
			$this->list($output);
			return;
		}

		$force = $input->getOption('force');

		try {
			$this->isInstalled();

			if ($force) { $this->install(); }
		} catch (EnvironmentNotConfigured $e) {
			$this->install();
		}
    }
}