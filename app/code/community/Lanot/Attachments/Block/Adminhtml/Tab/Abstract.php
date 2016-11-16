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

abstract class Lanot_Attachments_Block_Adminhtml_Tab_Abstract
    extends Mage_Uploader_Block_Single
{
    /**
     * @var Lanot_Attachments_Model_Mysql4_Attachments_Collection
     */
    protected $_collection = null;

    /**
     * @var Lanot_Attachments_Helper_Data
     */
    protected $_helper = null;

    /**
     * @var Varien_Io_File
     */
    protected $_io = null;

    /**
     * @var int
     */
    protected $_storeId = 0;

    public function _construct()
    {
        parent::_construct();

        $this->setTemplate('lanot/attachments/edit/attachments.phtml');
        $this->_storeId = $this->getRequest()->getParam('store', 0);
        $this->_io = new Varien_Io_File();
    }

    /**
     * @return Mage_Core_Model_Abstract|null
     */
    public function getEntity()
    {
        return null;
    }

    /**
     * Get model of the product that is being edited
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return Mage::registry('current_product');
    }
    /**
     * Check block is readonly
     *
     * @return boolean
     */
    public function isReadonly()
    {
         return $this->getProduct()->getDownloadableReadonly();
    }

    //PREPARE ADD and UPLOAD BUTTONS
    #Step 1.
     /**
     * Retrieve Upload button HTML
     *
     * @return string
     */
    public function getUploadButtonHtml()
    {
        return $this->getChild('upload_button')->toHtml();
    }

    /**
     * @return string
     */
    public function getBrowseButtonHtml()
    {
        return $this->getChild('browse_button')
            // Workaround for IE9
            ->setBeforeHtml('<div style="display:inline-block; " id="attachments_item_{{id}}_file-browse">')
            ->setAfterHtml('</div>')
            ->setId('attachments_item_{{id}}_file-browse')
            ->toHtml();
    }

    /**
     * @return string
     */
    public function getDeleteButtonHtml()
    {
        return $this->getChild('delete_button')
            ->setLabel('')
            ->setId('attachments_item_{{id}}_file-delete')
            ->setClass('delete')
            ->setStyle('display:none; width:31px;')
            ->toHtml();
    }


    /**
     * Retrieve config json
     *
     * @return string
     */
    public function getConfigJson()
    {
        $this->getUploaderConfig()
            ->setFileParameterName('lanot_attachments')
            ->setTarget(
                Mage::getModel('adminhtml/url')
                    ->addSessionParam()
                    ->getUrl('lanot_attachments/adminhtml_files/upload', array('type' => 'lanot_attachments', '_secure' => true))
            );
        $this->getMiscConfig()
            ->setReplaceBrowseWithRemove(true)
        ;
        return Mage::helper('core')->jsonEncode(parent::getJsonConfig());
    }

    /**
     * Prepare layout
     *
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->setChild(
            'upload_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->addData(array(
                    'id'      => '',
                    'label'   => Mage::helper('adminhtml')->__('Upload Files'),
                    'type'    => 'button',
                    'onclick' => 'Lanot_Attachments.massUploadByType(\'lanot_attachments\')'
                ))
        );

        $this->_addElementIdsMapping(array(
            'container' => $this->getHtmlId() . '-new',
            'delete'    => $this->getHtmlId() . '-delete'
        ));
    }
    /**
     * Retrieve config object
     *
     * @deprecated
     * @return $this
     */
    public function getConfig()
    {
        return $this;
    }   
    
    public function getAddButtonHtml()
    {
        $addButton = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label' => $this->_getHelper()->__('Add New Row'),
                'id'    => 'add_attachments_item',
                'class' => 'add',
            ));
        return $addButton->toHtml();
    }

    /**
     * @return Lanot_Attachments_Model_Mysql4_Attachments_Collection|null
     */
    public function getCollection()
    {
        return $this->_collection;
    }

    /**
     * @return array
     */
    public function getAttachmentsData()
    {
        if (!$this->getCollection()) {
            return array();
        }

        $rows = array();
        foreach ($this->getCollection() as $item) {
            $item->setStoreId($this->_storeId); //set store id for download url
            $this->_saveToStorage($item);
            $rows[] = new Varien_Object($this->_prepareItem($item));
        }
        return $rows;
    }

    /**
     * @return array
     */
    protected function _getUploaderPost()
    {
        return array(
            'form_key' => $this->getFormKey()
        );
    }

    /**
     * Prepare file data for flex uploader
     *
     * @param Lanot_Attachments_Model_Attachments $item
     * @return array
     */
    protected function _prepareItem(Lanot_Attachments_Model_Attachments $item)
    {
        $tmpItem = $item->toArray();
        $tmpItem['attachment_url'] = $item->getAttachmentUrl(); //fix for download from admin

        //prepare uploader data
        $file = $this->_getFileHelper()->getFilePath($this->_getConfigHelper()->getBasePath(), $item->getFile());
        if ($item->getFile() && $this->_io->fileExists($file)) {
            $tmpItem['file_save'] = array(
                array(
                    'file'   => $item->getFile(),
                    'name'   => $this->_getFileHelper()->getFileFromPathFile($item->getFile()),
                    'size'   => filesize($file),
                    'status' => 'old'
                )
            );
        }

        //set store title
        if ($this->getEntity() && $item->getStoreTitle()) {
            $tmpItem['store_title'] = $item->getStoreTitle();
        }
        return $tmpItem;
    }

    /**
     * Save to DB storage
     *
     * @param Lanot_Attachments_Model_Attachments $item
     * @return Lanot_Attachments_Block_Adminhtml_Tab_Abstract
     */
    protected function _saveToStorage(Lanot_Attachments_Model_Attachments $item)
    {
        $file = $this->_getFileHelper()->getFilePath($this->_getConfigHelper()->getBasePath(), $item->getFile());
        if ($item->getFile() && $this->_getConfigHelper()->canUseFileStorage()) {
            if (!$this->_io->fileExists($file)) {
                $this->_getHelperStorage()->saveFileToFilesystem($file);
            }
        }
        return $this;
    }

    /**
     * @return Lanot_Attachments_Model_Mysql4_Attachments_Collection
     */
    protected function _initCollection()
    {
        $collection = $this->_getAttachmentsModel()->getCollection();
        $collection->addTitleToFields($this->_storeId)->addLinkToFields($this->_storeId);
        return $collection;
    }

    /**
     * @return Lanot_Attachments_Model_Attachments
     */
    protected function _getAttachmentsModel()
    {
        return Mage::getModel('lanot_attachments/attachments');
    }

    /**
     * @return Lanot_Attachments_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('lanot_attachments');
    }

    /**
     * @return Lanot_Attachments_Helper_Config
     */
    protected function _getConfigHelper()
    {
        return Mage::helper('lanot_attachments/config');
    }

    /**
     * @return Lanot_Attachments_Helper_File
     */
    protected function _getFileHelper()
    {
        return Mage::helper('lanot_attachments/file');
    }

    /**
     * @return Mage_Core_Helper_File_Storage_Database
     */
    protected function _getHelperStorage()
    {
        return Mage::helper('core/file_storage_database');
    }
}
