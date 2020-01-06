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

	const VERSION_FAKE = '0.0.0';

	protected static $defaultName = 'install';

    protected function configure() : void
    {
		$this
			->setDescription('Install config')
			->setHelp('This command install config for lint')
			->addOption('list', 'l', InputOption::VALUE_NONE, 'List cached PHP instances')
			->addOption('force', 'f', InputOption::VALUE_NONE, 'Force install');
	}
	
	protected function isInstalled() : void
	{
		if (!Environment::isConfigured()) {
			throw new EnvironmentNotConfigured();
		}
	}

	protected function isOutdated() : bool
	{
		$application = $this->getApplication();
		$config = Environment::getConfig();
		$version = $config ? $config->get('version', static::VERSION_FAKE) : static::VERSION_FAKE;

		$parser = new VersionConstraintParser();
		
		$constraint = $parser->parse($application ? $application->getVersion() : static::VERSION_FAKE);

		return !$constraint->complies(new Version($version));
	}

	protected function install() : void
	{
		$application = $this->getApplication();
		(new Environment($application ? $application->getVersion() : static::VERSION_FAKE))->init();
	}

	protected function list(OutputInterface $output) : int
	{
		$list = [];

		if (Environment::isConfigured()) {
			$config = Environment::getConfig();
			$list = $config ? $config->get('paths', []) : [];
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

		return 0;
	}

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
		if ($input->getOption('list')) {
			return $this->list($output);
		}

		$force = $input->getOption('force');

		try {
			$this->isInstalled();

			if ($force || $this->isOutdated()) { $this->install(); }
		} catch (EnvironmentNotConfigured $e) {
			$this->install();
		}

		return 0;
    }
}