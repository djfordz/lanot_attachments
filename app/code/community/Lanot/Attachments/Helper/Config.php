<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category    Lanot
 * @package     Lanot_Attachments
 * @copyright   Copyright (c) 2012 Lanot
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Lanot_Attachments_Helper_Config
{
    protected $_productType = 'catalog_product';
    protected $_canUseFileStorage = null;
    protected $_storeId = 0;

    const XML_PATH_ENABLED           = 'lanot_attachments/%s/enabled';
    const XML_PATH_TITLE             = 'lanot_attachments/%s/title';
    const XML_PATH_CSS_CLASS_BLOCK   = 'lanot_attachments/%s/class';
    const XML_PATH_CSS_CLASS_TITLE   = 'lanot_attachments/%s/class_title';
    const XML_PATH_CSS_CLASS_CONTENT = 'lanot_attachments/%s/class_content';

    const XML_PATH_MISC_SHOW_ICONS   = 'lanot_attachments/%s/show_icons';
    const XML_PATH_MISC_NEW_WINDOW   = 'lanot_attachments/%s/new_window';
    const XML_PATH_MISC_URL_FILENAME = 'lanot_attachments/%s/url_filename';
    const XML_PATH_MISC_URL_REDIRECT = 'lanot_attachments/%s/url_redirect';

    public function __construct()
    {
        $this->_storeId = Mage::app()->getStore()->getId();
    }

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

    /**
     * @param string $type
     * @return bool
     */
    public function getTitle($type)
    {
        return $this->_getConfigValue(self::XML_PATH_TITLE, $type);
    }

    /**
     * @param string $type
     * @return bool
     */
    public function getClassBlock($type)
    {
        return $this->_getConfigValue(self::XML_PATH_CSS_CLASS_BLOCK, $type);
    }

    /**
     * @param string $type
     * @return bool
     */
    public function getClassTitle($type)
    {
        return $this->_getConfigValue(self::XML_PATH_CSS_CLASS_TITLE, $type);
    }

    /**
     * @param string $type
     * @return bool
     */
    public function getClassContent($type)
    {
        return $this->_getConfigValue(self::XML_PATH_CSS_CLASS_CONTENT, $type);
    }

    /**
     * @param string $type
     * @return bool
     */
    public function isEnabled($type)
    {
        return (bool) $this->_getConfigValue(self::XML_PATH_ENABLED, $type);
    }

    /**
     * @return bool
     */
    public function canShowIcons()
    {
        return (bool) $this->_getConfigValue(self::XML_PATH_MISC_SHOW_ICONS);
    }

    /**
     * @return bool
     */
    public function canShowNewWindow()
    {
        return (bool) $this->_getConfigValue(self::XML_PATH_MISC_NEW_WINDOW);
    }

    /**
     * @return bool
     */
    public function canShowUrlFilename()
    {
        return (bool) $this->_getConfigValue(self::XML_PATH_MISC_URL_FILENAME);
    }

    /**
     * @return bool
     */
    public function canUseUrlRedirect()
    {
        return (bool) $this->_getConfigValue(self::XML_PATH_MISC_URL_REDIRECT);
    }

    /**
     * @return bool
     */
    public function canUseFileStorage()
    {
        if (null === $this->_canUseFileStorage) {
            $file = Mage::getBaseDir('code');
            $file .= '/core/Mage/Core/Helper/File/Storage/Database.php';
            $this->_canUseFileStorage = file_exists($file);
        }
        return $this->_canUseFileStorage;
    }

    /**
     * @return string
     */
    public function getCacheTag($type)
    {
        if ($type == 'catalog_product') {
            return Mage_Catalog_Model_Product::CACHE_TAG;
        }
        return 'attachments';
    }

    /**
     * @return string
     */
    public function getCacheKey($type, $id, $sId)
    {
        return 'lanot_attachments_' . $this->getCacheTag($type) . '_e' . $id . '_st' . $sId;
    }

    /**
     * @param $path
     * @param $type
     * @return string
     */
    protected function _getConfigValue($path, $type = 'view')
    {
        return Mage::getStoreConfig(sprintf($path, $type), $this->_storeId);
    }
}
