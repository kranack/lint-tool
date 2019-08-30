<?php

namespace kranack\Lint\Env\Scanner;

interface IScanner
{

	public function detect() : bool;

	public function scan() : array;

	public function isPathValid(string $path) : bool;

	public function extractVersion(string $path) : string;

}