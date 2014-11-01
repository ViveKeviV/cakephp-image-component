<?php
//App:uses('Component', 'Controller');
class ImageComponent extends Component {
	
	/**
	 * Global variables to initialize
	 *
	 * @var string
	 */
	public $rootDir;
	public $sourceLocation = '/app/webroot/files/images/';
	public $destinationLocation = '/app/webroot/files/';
	public $date;
	
	/**
	 * Constructor of class ImageComponent.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->rootDir = getcwd();
		$date = new DateTime();
		$this->date = $date->getTimestamp();
	}
	
	/**
	 * Resizes the given image into new dimension and save it in desired location.
	 * 
	 * @params mixed (string, int, int)
	 * @return newImgLocation
	 */
	public function resize($imgName=null, $width=null, $height=null) {
		if (empty($imgName)) {
			throw new NotFoundException(__('FileName is empty'));
		}
		
		if (empty($width) || empty($height) || !$this->is_round($width) || !$this->is_round($height)) {
			throw new MissingBehaviorException(__('Width or Height is not a numeric value or round number'));
		} else {
			$w = $width;
			$h = $height;
		}
		
		$type = $this->getImageType($imgName);
		$onlyname = str_replace($type,'',$imgName);
		$location = $this->getImageSourceLocation();
		$ImagewithLocation = $location.$imgName;
		
		move_uploaded_file($onlyname, $imgName);
		
		if ($type == 'jpeg') $type = 'jpg';
		
		list($width_orig, $height_orig) = getimagesize($ImagewithLocation);

		$ratio_orig = $width_orig/$height_orig;

		if ($width/$height > $ratio_orig) {
		   $width = $height*$ratio_orig;
		} else {
		   $height = $width/$ratio_orig;
		}
		
		$newImage = imagecreatetruecolor($width, $height);
		
		// preserve transparency
		if ($type == "gif" or $type == "png") {
			imagecolortransparent($newImage, imagecolorallocatealpha($newImage, 0, 0, 0, 127));
			imagealphablending($newImage, false);
			imagesavealpha($newImage, true);
		}
		
		$sourceImg = $this->createImagewithType($imgName, $type, $ImagewithLocation);
		
		imagecopyresampled($newImage, $sourceImg, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
		$newDir = $w.'x'.$h;
		$destinationLocation = $this->getImageDestinationLocation($newDir);
		return $this->DrawImagebyType($newImage, $imgName, $type, $destinationLocation);
	}
	
	/**
	 * Draw image by its type
	 * 
	 * @params same(string, string, string)
	 * @return void
	 */
	private function DrawImagebyType($newImage, $imgName, $type, $destinationLocation) {
		$tmpLocation = $destinationLocation.$imgName;
		if (!file_exists($tmpLocation)) {
			$tmpName = str_replace('.'.$type,'',$imgName).$this->date;
			$destinationLocation = $destinationLocation.$tmpName.'.'.$type;
		}
		switch ($type) {
			case 'gif': imagegif($newImage, $destinationLocation); break;
			case 'jpg': imagejpeg($newImage, $destinationLocation); break;
			case 'png': imagepng($newImage, $destinationLocation); break;
			default : throw new NotFoundException(__('Unsupported picture type!'));
		}
		$destinationLocation = str_replace($this->rootDir.'/app/webroot/', '', $destinationLocation);
		return $destinationLocation;
	}

	/**
	 * Creates image from its type
	 * 
	 * @params same(string, string, string)
	 * @return sourceImage
	 */
	private function createImagewithType($imgName, $type, $ImageLocation) {
		switch ($type) {
			case 'gif': $sourceImg = imagecreatefromgif($ImageLocation); break;
			case 'jpg': $sourceImg = imagecreatefromjpeg($ImageLocation); break;
			case 'png': $sourceImg = imagecreatefrompng($ImageLocation); break;
			default : throw new NotFoundException(__('Unsupported picture type!'));
		}
		return $sourceImg;
	}

	/**
	 * Detects that the given width or height is a round value
	 * 
	 * @params any(value)
	 * @return numeric/round values
	 */
	private function is_round($value) {
		return is_numeric($value) && intval($value) == $value;
	}
	
	/**
	 * Gives the image destination location to save the image
	 * 
	 * @params (string)
	 * @return location
	 */
	private function getImageDestinationLocation($newDir) {
		$location = $this->rootDir.$this->destinationLocation.$newDir;
		if (!file_exists($location) && !is_dir($location)) {
			$mask = umask(0);
			mkdir($location, 0777, 1);
			umask($mask);
		}
		$location = $location.'/';
		return $location;
	}

	/**
	 * Gives the image location to find the image
	 * 
	 * @return location
	 */
	private function getImageSourceLocation() {
		$location = $this->rootDir.$this->sourceLocation;
		if (!file_exists($location) && !is_dir($location)) {
			mkdir($location);
		}
		return $location;
	}

	/**
	 * Gives the image type from the imagename
	 * 
	 * @params (string)
	 * @return type
	 */
	private function getImageType($imgName) {
		return str_replace('.','',strtolower(strrchr($imgName,'.')));
	}
	
	
}
?>
