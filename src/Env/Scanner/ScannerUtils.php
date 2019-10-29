<?php

namespace kranack\Lint\Env\Scanner;

class ScannerUtils
{

	public static function extractVersion(string $path) : string
	{
		exec(escapeshellcmd($path) . ' -v', $lines);
		$firstLine = reset($lines);

		if ($firstLine !== null) {
			$version = strstr($firstLine, '(cli)', true);

			if ($version !== false) {
				$version = trim(str_replace('PHP', '', $version));
				
				$parts = explode('-', $version);
				
				return (count($parts) > 1 ? reset($parts) : $version);
			}
		}

		return '1.0.0';
	}

}