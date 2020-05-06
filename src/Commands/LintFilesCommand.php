<?php

namespace kranack\Lint\Commands;

use stdClass;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\{ InputArgument, InputOption };
use Symfony\Component\Console\Output\OutputInterface;

use JakubOnderka\PhpParallelLint\{ ConsoleWriter, Manager, Settings };

use PharIo\Version\Version;
use PharIo\Version\VersionConstraintParser;

use kranack\Lint\Env\Environment;
use kranack\Lint\Env\Scanner\Scanner_Type;
use kranack\Lint\Exceptions\EnvironmentNotConfigured;
use kranack\Lint\Output\ConsoleOutput;

class LintFilesCommand extends Command
{
	protected static $defaultName = 'lint';

    protected function configure() : void
    {
		$this
			->setDescription('Lint files')
			->setHelp('This command lint PHP files')
			->addArgument('folder', InputArgument::REQUIRED, 'The folder containing PHP files')
			->addOption('min', 'm', InputOption::VALUE_REQUIRED, 'The minimal PHP version')
			->addOption('exclude', null, InputOption::VALUE_REQUIRED, 'Path to exclude')
			->addOption('colors', null, InputOption::VALUE_NONE, 'Force ANSI colors')
			->addOption('full', null, InputOption::VALUE_NONE, 'Force all PHP binaries to be used for linting')
			->addOption('no-local', null, InputOption::VALUE_NONE, 'Force all local PHP binaries to be ignored');
	}
	
	protected function isInstalled() : void
	{
		if (!Environment::isConfigured()) {
			throw new EnvironmentNotConfigured();
		}
	}

	protected function versionMatch(string $minVersion, string $actualVersion) : bool
	{
		$parser = new VersionConstraintParser();
		$constraint = $parser->parse($minVersion);

		return $constraint->complies(new Version($actualVersion));
	}

	protected function buildArguments(string $folder, string $path, stdClass $options) : array
	{
		$exclude = $options->exclude ?: null;
		$colors = $options->colors ?: false;

		$args = [ '', '-p', $path ];

		if ($exclude) {
			$args [] = '--exclude';
			$args [] = $exclude;
		}

		if ($colors) { $args [] = '--colors'; }

		$args [] = $folder;

		return $args;
	}


    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
		$this->isInstalled();
		$config = Environment::getConfig();
		$phpExecs = $config ? $config->get('paths', []) : [];
		$usedExecs = [];

		$folder = $input->getArgument('folder');
		$version = $input->getOption('min') ?? ($config ? $config->get('min') : null) ?? '^7.1';
		$exclude = $input->getOption('exclude') ?? '';
		$colors = $input->getOption('colors') ?? false;
		$full = $input->getOption('full') ?? false;
		$noLocal = $input->getOption('no-local') ?? false;

		if ($colors) $output->setDecorated(true);

		$count = 0;
		foreach ($phpExecs as $exec) {
			if (!is_object($exec)) continue;

			$execVersion = $exec->version ?? Environment::extractVersion($exec);

			// If the PHP exec version does not match the constraint then skip it
			if (!$this->versionMatch($version, $execVersion)) continue;

			// If no-local mode then skip all local PHP exec
			if ($noLocal && $exec->type === Scanner_Type::LOCAL) continue;

			// If non-full mode then search for an already used matching PHP exec
			if (!$full) {
				foreach ($usedExecs as $_exec) {
					$_execVersion = $_exec->version ?? Environment::extractVersion($_exec);

					if ($this->versionMatch(sprintf('~%s', $_execVersion), $execVersion) || $this->versionMatch(sprintf('~%s', $execVersion), $_execVersion)) continue 2;
				}
			}

			if ($count) $output->writeln('');

			// Do lint
			$settings = Settings::parseArguments($this->buildArguments($folder, $exec->path, (object) [ 'exclude' => $exclude, 'colors' => $colors ]));
			
			$_output = new ConsoleOutput(new ConsoleWriter());
			$_output->redirectOutput($output);

			$manager = new Manager();
			$manager->setOutput($_output);
			$manager->run($settings);

			++$count;
			$usedExecs [] = $exec;
		}

		return 0;
    }
}