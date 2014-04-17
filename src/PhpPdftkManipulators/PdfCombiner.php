<?php

namespace PhpPdftkManipulators;

class PdfCombiner
{
	private $pdftkBinary;

	public function __construct($pdftkBinary)
	{
		$this->pdftkBinary = $pdftkBinary;
	}

    /**
     * @param $original
     * @param $background
     * @return string
     * @throws \RuntimeException
     */
    public function addBackground($original, $background)
	{
		$sourceFile = tempnam(sys_get_temp_dir(), 'pdf');
		file_put_contents($sourceFile, $original);

		$backgroundFile = tempnam(sys_get_temp_dir(), 'pdf');
		file_put_contents($backgroundFile, $background);

		$outputFile = tempnam(sys_get_temp_dir(), 'pdf');

		$command = "{$this->pdftkBinary} $sourceFile background $backgroundFile output $outputFile verbose";
		$execOutput = array();
		exec($command, $execOutput);
		$result = file_get_contents($outputFile);
		if(!$result) {
			throw new \RuntimeException("Couldn't add background to PDF: ".PHP_EOL.$command.PHP_EOL.implode(PHP_EOL, $execOutput));
		}

		@unlink($sourceFile);
		@unlink($backgroundFile);
		@unlink($outputFile);

		return $result;
	}

    /**
     * @param array $originals
     * @return string
     * @throws \RuntimeException
     */
    public function concatenate(array $originals)
	{
		$sourceFiles = array();
		foreach($originals as $k => $original)
		{
			$sourceFiles[$k] = tempnam(sys_get_temp_dir(), 'pdf');
			file_put_contents($sourceFiles[$k], $original);
		}

		$outputFile = tempnam(sys_get_temp_dir(), 'pdf');

		$command = sprintf("{$this->pdftkBinary} %s cat output $outputFile", implode(' ', $sourceFiles));
		exec($command);
		$result = file_get_contents($outputFile);
		if(!$result) {
			throw new \RuntimeException("Couldn't catenate PDFs");
		}

		foreach($sourceFiles as $sourceFile) {
			@unlink($sourceFile);
		}
		@unlink($outputFile);

		return $result;
	}
}