<?php

/**
 * @return mixed
 */
function requireComposerAutoload() {
	foreach([ __DIR__ . '/../vendor/autoload.php', __DIR__ . '/../../../autoload.php'] as $path) {
		if (file_exists($path)) {
			define('LINT_COMPOSER_FOLDER', dirname(realpath($path)));
			$root = LINT_COMPOSER_FOLDER;

			do {
				$root = dirname($root);
			} while (!file_exists($root . DIRECTORY_SEPARATOR . 'composer.json') && ($root !== '/' && $root !== '\\'));

			if ($root !== '/' && $root !== '\\') {
				define('LINT_ROOT_FOLDER', $root);
			}
			return require_once($path);
		}
	}

	die('You need to set up the project dependencies using Composer' . PHP_EOL);
}

requireComposerAutoload();