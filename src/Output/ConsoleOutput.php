<?php

namespace kranack\Lint\Output;

use Symfony\Component\Console\Output\OutputInterface;

use JakubOnderka\PhpParallelLint\{ ConsoleWriter, ErrorFormatter, TextOutput, Output, Result };

class ConsoleOutput extends TextOutput
{

	private OutputInterface $output;

	/**
     * @param int $phpVersion
     * @param int $parallelJobs
     * @param string $hhvmVersion
     */
    public function writeHeader($phpVersion, $parallelJobs, $hhvmVersion = null)
    {
        $this->write("<fg=blue;options=bold>PHP {$this->phpVersionIdToString($phpVersion)} | ");

        if ($hhvmVersion) {
            $this->write("HHVM $hhvmVersion | ");
        }

        if ($parallelJobs === 1) {
            $this->writeLine("1 job</>");
        } else {
            $this->writeLine("{$parallelJobs} parallel jobs</>");
        }
    }

	/**
     * @param string $string
     * @param string $type
     */
    public function write($string, $type = self::TYPE_DEFAULT)
    {
		
		switch ($type) {
			case self::TYPE_ERROR:
				$string = sprintf('%s%s%s', '<fg=red>', $string, '</>');
				break;
			case self::TYPE_SKIP:
				$string = sprintf('%s%s%s', '<fg=yellow>', $string, '</>');
				break;
			case self::TYPE_OK:
				$string = sprintf('%s%s%s', '<fg=green>', $string, '</>');
				break;
		}


        $this->output->write($string);
	}
	
	/**
     * @param string|null $line
     * @param string $type
     */
    public function writeLine($line = null, $type = self::TYPE_DEFAULT)
    {
		$line = $line ?? '';

		switch ($type) {
			case self::TYPE_ERROR:
				$line = sprintf('%s%s%s', '<fg=red>', $line, '</>');
				break;
			case self::TYPE_SKIP:
				$line = sprintf('%s%s%s', '<fg=yellow>', $line, '</>');
				break;
			case self::TYPE_OK:
				$line = sprintf('%s%s%s', '<fg=green>', $line, '</>');
				break;
		}

        $this->output->writeln($line);
    }
	
	public function redirectOutput(OutputInterface $output)
	{
		$this->output = $output;
	}
}