CakePHP Image Component
=======================

This component gives you the ability to resize images in aspect ratio, also without losing the transparency of png/gif images. This component reads the source location of image and resize image and saves it destination location.

## Version
This is a basic version 0.0.1

The following things will be covered the following versions:
* While uploading a image with predefined size will be resized.
* Selecting customized paths for both source & destination.

## Requirements & Compatibility

* CakePHP 2.x
* PHP 5.3.x
* PHP GD Graphics library extension should be enabled in Server.

## Installation

Place the ImageComponent.php file in the root folder of the following:

`/app/Controller/Component/ImageComponent.php`

Add the following lines, in which controller you wants to include the component.

Example as below:-

File:
`/app/Controller/UsersController.php`

Code
```php <?php
class UsersController extends AppController {
    public $components = array('Image');
    ...
}
?>```

Add the following line to the method in controller, in which you to resize the image.
Syntax:
```php <?php
$this->Image->resize(imagename.extension, width, height);
?>```

Example:
```php <?php
$this->Image->resize('example.png', 500, 200);
?>``` 

To edit the source and destination location check the following for this code.
File:
`/app/Controller/Component/ImageComponent.php`

Code:
```php <?php
class ImageComponent extends Component {
    
    /**
     * Global variables to initialize
     *
     * @var string
     */
    public $rootDir;
    public $sourceLocation = '/files/images/';
    public $destinationLocation = '/files/';
    public $date;
    ...
?>```

## Contributors

[ViveKeviV](https://github.com/ViveKeviV)
