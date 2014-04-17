<?php
namespace PhpPdftkManipulators;

use Imagick;

class PdfToImageConverter
{
	private $width;
	private $height;
	
	public function __construct($width, $height)
	{
		$this->width = $width;
		$this->height = $height;
	}

    /**
     * @param $original string The pdf-contents
     * @return array One image per pdf-page
     */
    public function convert($original)
	{
		$sourceFile = tempnam(sys_get_temp_dir(), 'pdf');
		file_put_contents($sourceFile, $original);
		
		$im = new Imagick($sourceFile);
		$count = $im->getNumberImages();
		$results = array();

		for($i = 0; $i < $count; $i++)
		{
			$sourcePdf = new Imagick();
			$sourcePdf->readImage($sourceFile."[$i]");
			$originalHeight = $sourcePdf->getimageheight();
			$originalWidth = $sourcePdf->getimagewidth();
			
			$image = new Imagick();
			$image->newImage($originalWidth, $originalHeight, "white");
			$image->setimagecolorspace($sourcePdf->getimagecolorspace());
			$image->setbackgroundcolor("white");
			
			$image->compositeimage($sourcePdf, Imagick::COMPOSITE_OVER, 0, 0);
			
			$image->setImageFormat('jpg');
			$image->setImageUnits(Imagick::RESOLUTION_PIXELSPERINCH);
			$image->setImageCompression(Imagick::COMPRESSION_JPEG);
			$image->setImageCompressionQuality(75);
			
			$image->resizeimage($this->width, $this->height, Imagick::FILTER_LANCZOS, 1, true);
			$results[$i] = $image->getImageBlob(); 
			
			$image->clear();
			$image->destroy();
		}
		
		@unlink($sourceFile);
		return $results;
	}
}

