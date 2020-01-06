<?php

function requireComposerAutoload()
{
	foreach([ __DIR__ . '/../vendor/autoload.php', __DIR__ . '/../../../autoload.php'] as $path) {
		if (file_exists($path)) {
			define('LINT_COMPOSER_FOLDER', dirname(realpath($path)));
			define('LINT_ROOT_FOLDER', dirname(LINT_COMPOSER_FOLDER));
			return require_once($path);
		}
	}

	die('You need to set up the project dependencies using Composer' . PHP_EOL);
}

requireComposerAutoload();