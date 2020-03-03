<?php 

namespace kranack\Lint\Env\Scanner;

abstract class Scanner_Type
{
	const LOCAL = 'Local';
	const MACPORTS = 'Macports';
	const HOMEBREW = 'Homebrew';
}