<?php

namespace kranack\Lint\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use PharIo\Version\Version;
use PharIo\Version\VersionConstraintParser;

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

	protected function isOutdated()
	{
		$version = Environment::getConfig()->get('version', '0.0.0');

		$parser = new VersionConstraintParser();
		$constraint = $parser->parse($this->getApplication()->getVersion());

		return !$constraint->complies(new Version($version));
	}

	protected function install()
	{
		(new Environment($this->getApplication()->getVersion()))->init();
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
			$output->writeln(sprintf('<fg=cyan;options=bold>Version</> %s', ($path->version ?? '-')));

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

			if ($force || $this->isOutdated()) { $this->install(); }
		} catch (EnvironmentNotConfigured $e) {
			$this->install();
		}
    }
}