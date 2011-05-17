<?php
/**
 * TopOSScz CPost module
 *
 * @category   TopOSScz
 * @package    TopOSScz_CPost
 * @copyright  Copyright (c) 2011 TopOSS.cz (Josef Jezek) <magento@toposs.cz>
 * @license    http://www.opensource.org/licenses/gpl-3.0.html GNU General Public License version 3
 */

class TopOSScz_CPost_Block_Export_Invoice extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
	$this->_blockGroup = 'toposscz_cpost';
        $this->_controller = 'export_invoice';
        $this->_headerText = Mage::helper('toposscz_cpost')->__('Export to CPost by Invoices');
        parent::__construct();
        $this->_removeButton('add');
    }

    public function getPaymentInfoHtml()
    {
        return $this->getChildHtml('payment_info');
    }
}
