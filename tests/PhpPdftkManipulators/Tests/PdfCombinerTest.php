<?php

namespace Tests\PhpPdftkManipulators;

use PhpPdftkManipulators\PdfCombiner;

class PdfCombinerTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @test
	 * @medium
	 */
	public function adds_background()
	{
		$pdfCombiner = new PdfCombiner('pdftk');

		$result = $pdfCombiner->addBackground(
			file_get_contents(__DIR__.'/fixtures/sample_bw.pdf'),
			file_get_contents(__DIR__.'/fixtures/sample_color.pdf')
		);

        file_put_contents(__DIR__.'/fixtures/expected_background.pdf', $result);

		$this->assertGreaterThan(0, strlen($result), "The resulting pdf should not be zero characters");
		$this->assertEquals(file_get_contents(__DIR__.'/fixtures/expected_background.pdf'), $result);
	}

	/**
	 * @test
	 * @medium
	 */
	public function concatenates()
	{
		$pdfCombiner = new PdfCombiner('pdftk');
		$result = $pdfCombiner->concatenate(array(
			file_get_contents(__DIR__.'/fixtures/sample_bw.pdf'),
			file_get_contents(__DIR__.'/fixtures/sample_color.pdf'),
		));

        file_put_contents(__DIR__.'/fixtures/expected_concatenated.pdf', $result);

		$expected = file_get_contents(__DIR__.'/fixtures/expected_concatenated.pdf');
		$this->assertGreaterThan(0, strlen($result), "The resulting pdf should not be zero characters");
		$this->assertStringDistanceInPercent('99', $expected, $result);
	}

	/**
	 * @test
	 * @medium
	 */
	public function concatenates_pdfs_from_file_paths()
	{
		$pdfCombiner = new PdfCombiner('pdftk');
		$result = $pdfCombiner->concatenateFromFilePaths(array(
			(__DIR__.'/fixtures/sample_bw.pdf'),
			(__DIR__.'/fixtures/sample_color.pdf'),
		));

        file_put_contents(__DIR__.'/fixtures/expected_concatenated.pdf', $result);

		$expected = file_get_contents(__DIR__.'/fixtures/expected_concatenated.pdf');
		$this->assertGreaterThan(0, strlen($result), "The resulting pdf should not be zero characters");
		$this->assertStringDistanceInPercent('99', $expected, $result);
	}

	private function assertStringDistanceInPercent($minimumPercentage, $expected, $actual)
	{
		$percentage = 0;
		similar_text($expected, $actual, $percentage);
		$this->assertGreaterThanOrEqual($minimumPercentage, $percentage, "The distance between the strings should be greater than or equal to $minimumPercentage%, got $percentage%");
	}

}

