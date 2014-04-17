<?php
namespace Tests\PhpPdftkManipulators;

use PhpPdftkManipulators\PdfToImageConverter;

class PdfToImageConverterTest extends \PHPUnit_Framework_TestCase
{
	public function providePdfs()
	{
		return array(
			//array(__DIR__.'/fixtures/fpdf_bw.pdf', __DIR__.'/fixtures/result_fpdf_bw.jpg'),
		);
	}

    /** @test */
    public function imagick_is_installed()
    {
        $this->assertTrue(class_exists('\Imagick'), 'Imagick is not installed');
    }

	/**
	 * @test
	 * @dataProvider providePdfs
	 * @medium
	 * @depends imagick_is_installed
	 */
	public function converts_to_jpg($original, $location)
	{
		$converter = new PdfToImageConverter(640, 905);
		$results = $converter->convert(file_get_contents($original));
	
		foreach($results as $result) {
			file_put_contents($location, $result);
		}
		
		$this->assertCount(1, $results);
	}
	

}