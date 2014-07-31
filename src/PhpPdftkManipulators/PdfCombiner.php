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
		$sourceFilePaths = array();
		foreach($originals as $k => $original)
		{
			$sourceFilePaths[$k] = tempnam(sys_get_temp_dir(), 'pdf');
			file_put_contents($sourceFilePaths[$k], $original);
		}

        $result =  $this->concatenateFromFilePaths($sourceFilePaths);

        foreach($sourceFilePaths as $sourceFile) {
            @unlink($sourceFile);
        }

        return $result;

    }

    /**
     * @param string[] $sourceFilePaths
     * @return string
     */
    public function concatenateFromFilePaths($sourceFilePaths)
    {

        $outputFile = tempnam(sys_get_temp_dir(), 'pdf');

        $command = sprintf("{$this->pdftkBinary} %s cat output $outputFile", implode(' ', $sourceFilePaths));
        exec($command, $output, $returnCode); 
        
        if($returnCode != 0) { 
            throw new \RuntimeException("Couldn't catenate PDFs"); 
        }
        
        $result = file_get_contents($outputFile);
        if(!$result) {
            throw new \RuntimeException("Couldn't catenate PDFs");
        }

        @unlink($outputFile);
        return $result;
    }
}
