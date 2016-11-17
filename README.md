##Fix for Lanot Attachments Magento Extension to use Javascript Uploader instead of Flash Uploader
(note: this is for Lanot Attachments Version that does not have MassAttachments, it still can be used in version with MassAttachments, however this is only fix for Product Attachments Upload, single file.);

I suggest upgrading to latest version before patching.

Lanot Attachments moves a static function from Model/Attachments called `function getBasePath` and `function getBaseTmpPath` from the model to `Helper/Config.php` if you have not upgraded your version of Lanot Attachments, you will have to copy these functions to the Helper `Config.php` and remove the static reference when copying but keep the static function in the Model as well.

~~~
/**
 * @return string
 */
public function getBaseTmpPath()
{
	Mage::log(Mage::getBaseDir('media') . DS . 'tmp' . DS . 'lanot' . DS . 'attachments');
	return Mage::getBaseDir('media') . DS . 'tmp' . DS . 'lanot' . DS . 'attachments';
}

/**
 * @return string
 */
public function getBasePath()
{
	Mage::log(Mage::getBaseDir('media') . DS. 'lanot' . DS . 'attachments');
	return Mage::getBaseDir('media') . DS . 'lanot' . DS . 'attachments';
}

~~~
