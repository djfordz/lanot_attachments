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

class Lanot_Attachments_Block_Adminhtml_Product_Edit
extends Lanot_Attachments_Block_Adminhtml_Abstract
{
	protected $_block = 'lanot_attachments/adminhtml_product_tab';
		
	public function canShowTab()
	{
        /* @var $helper Lanot_Attachments_Helper_Admin */
        $helper = Mage::helper('lanot_attachments/admin');
        $isAllowed = $helper->isActionAllowed('manage_attachments');

		$setId = false;
		$product = Mage::registry('current_product');
		
		if ($product) {
			$setId = $product->getAttributeSetId();
		}		
		if (!$setId) { 
			$setId = $this->getRequest()->getParam('set', null);
		}
				
		return ($setId && $isAllowed) ? true : false;
	}	
}