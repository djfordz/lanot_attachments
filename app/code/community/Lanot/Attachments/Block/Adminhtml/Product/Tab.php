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
 
class Lanot_Attachments_Block_Adminhtml_Product_Tab
extends Lanot_Attachments_Block_Adminhtml_Tab_Abstract
{
	//protected $_type_id = 'catalog_product';	
	public function getEntity()
	{
		return Mage::registry('current_product');		
	}

    public function getCollection()
    {
        if (null === $this->_collection && $this->getEntity()) {
            $this->_collection = $this->_initCollection()->useProduct($this->getEntity());
            $this->_collection->addOrder('sort_order', Varien_Data_Collection::SORT_ORDER_ASC);
        }
        return parent::getCollection();
    }
}