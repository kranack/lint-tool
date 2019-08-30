<?php

namespace kranack\Lint\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

use JakubOnderka\PhpParallelLint\{ ConsoleWriter, Manager, Settings };

use PharIo\Version\Version;
use PharIo\Version\VersionConstraintParser;

use kranack\Lint\Env\Environment;
use kranack\Lint\Exceptions\EnvironmentNotConfigured;
use kranack\Lint\Output\ConsoleOutput;

class LintFilesCommand extends Command
{
	protected static $defaultName = 'lint';

    protected function configure()
    {
		$this
			->setDescription('Lint files')
			->setHelp('This command lint PHP files')
			->addArgument('folder', InputArgument::REQUIRED, 'The folder containing PHP files')
			->addArgument('version', InputArgument::OPTIONAL, 'The minimal PHP version');
	}
	
	protected function isInstalled()
	{
		if (!Environment::isConfigured()) {
			throw new EnvironmentNotConfigured();
		}
	}

	protected function versionMatch(string $minVersion, string $actualVersion)
	{
		$parser = new VersionConstraintParser();
		$constraint = $parser->parse($minVersion);

		return $constraint->complies(new Version($actualVersion));
	}

    protected function execute(InputInterface $input, OutputInterface $output)
    {
		$this->isInstalled();
		$config = Environment::getConfig()->open();
		$phpExecs = $config->get('php', []);

		$folder = $input->getArgument('folder');
		$version = $input->getArgument('version') ?? $config->get('version') ?? '^7.1';

		$count = 0;
		foreach ($phpExecs as $exec) {
			if (!$this->versionMatch($version, Environment::extractVersion($exec))) continue;

			if ($count) $output->writeln('');

			// Do lint
			$settings = Settings::parseArguments([ '', '-p', $exec, $folder ]);
			
			$_output = new ConsoleOutput(new ConsoleWriter());
			$_output->redirectOutput($output);

			$manager = new Manager();
			$manager->setOutput($_output);
			$manager->run($settings);

			++$count;
		}

    }
}