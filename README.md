##Fix for Lanot Attachments Magento Extension to use Javascript Uploader instead of Flash Uploader
(note: this is for Lanot Attachments Version that does not have MassAttachments, it still can be used in version with MassAttachments, however this is only fix for Product Attachments Upload, single file.);

I suggest upgrading to latest version before patching.

If you have not upgraded your version of Lanot Attachments, you will have to add these functions to `Helper/Config.php` for this fix to work on older versions of Lanot Attachments.

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
