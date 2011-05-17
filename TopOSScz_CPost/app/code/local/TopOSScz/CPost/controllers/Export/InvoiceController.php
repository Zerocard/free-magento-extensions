<?php
/**
 * TopOSScz CPost module
 *
 * @category   TopOSScz
 * @package    TopOSScz_CPost
 * @copyright  Copyright (c) 2011 TopOSS.cz (Josef Jezek) <magento@toposs.cz>
 * @license    http://www.opensource.org/licenses/gpl-3.0.html GNU General Public License version 3
 */

class TopOSScz_CPost_Export_InvoiceController extends Mage_Adminhtml_Controller_Action 
{

    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->setUsedModuleName('TopOSScz_CPost');
    }

    /**
     * Main action : show orders list
     */
    public function indexAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('toposscz_cpost/export_invoice')
            ->_addContent($this->getLayout()->createBlock('toposscz_cpost/export_invoice'))
            ->renderLayout();
    }

    /**
     * Export Action
     * Generates a CSV file to download
     */
    public function exportAction()
    {
	    /* get the orders */
        $invoiceIds = $this->getRequest()->getPost('invoice_ids');

        /**
         * Get configuration
         */
        $separator = Mage::helper('toposscz_cpost')->getConfigurationFieldSeparator();
        $delimiter = Mage::helper('toposscz_cpost')->getConfigurationFieldDelimiter();
        if ($delimiter == 'simple_quote') {
            $delimiter = "'";
        } else if ($delimiter == 'double_quotes') {
            $delimiter = '"';
        }
        $lineBreak = Mage::helper('toposscz_cpost')->getConfigurationEndOfLineCharacter();
        if ($lineBreak == 'lf') {
            $lineBreak = "\n";
        } else if ($lineBreak == 'cr') {
            $lineBreak = "\r";
        } else if ($lineBreak == 'crlf') {
            $lineBreak = "\r\n";
        }
        $fileExtension = Mage::helper('toposscz_cpost')->getConfigurationFileExtension();
        $fileCharset = Mage::helper('toposscz_cpost')->getConfigurationFileCharset();

        /* set the filename */
        $filename = 'cpost_'.Mage::getSingleton('core/date')->date('Ymd_His').$fileExtension;

        /* initialize the content variable */
        $content = '';

        if (!empty($invoiceIds)) {
            foreach ($invoiceIds as $invoiceId) {

	        /* get the invoice and order */
                $invoice = Mage::getModel('sales/order_invoice')->load($invoiceId);
		$orderId = $invoice->getOrderId();
                $order = Mage::getModel('sales/order')->load($orderId);

        	$cod = 0;
        	if ($order->getPayment()->getMethod() == 'cashondelivery')
            		$cod = $order->getData('grand_total');
		
		if ($order->getWeight() <= 2) {
        		$parcelType = 'BA';
        		$specifiedPrice = 0;
			if ($cod == 0)
				$services = '';
			else
				$services = '4';
		} else {
        		$parcelType = 'BO';
        		$specifiedPrice = ceil($order->getData('grand_total')/1000)*1000;
			$services = '7+41';
		}

                /* get the shipping address */
                $address = $order->getShippingAddress();

                /* Invoice id */
                $content = $this->_addFieldToCsv($content, $delimiter, $invoice->getIncrementId());
                $content .= $separator;
                $content = $this->_addFieldToCsv($content, $delimiter, $cod);
                $content .= $separator;
                $content = $this->_addFieldToCsv($content, $delimiter, $specifiedPrice);
                $content .= $separator;
                $content = $this->_addFieldToCsv($content, $delimiter, $parcelType);
                $content .= $separator;
                $content = $this->_addFieldToCsv($content, $delimiter, $services);
                $content .= $separator;

                /* real order id */
                //$content = $this->_addFieldToCsv($content, $delimiter, $order->getRealOrderId());
                //$content .= $separator;
                /* customer name */
                $content = $this->_addFieldToCsv($content, $delimiter, $address->getName());
                $content .= $separator;
                /* customer company */
                $content = $this->_addFieldToCsv($content, $delimiter, $address->getCompany());
                $content .= $separator;
                /* street address, on 2 fields */
                $content = $this->_addFieldToCsv($content, $delimiter, $address->getStreet(1).' '.$address->getStreet(2));
                $content .= $separator;
                /* postal code */
                $content = $this->_addFieldToCsv($content, $delimiter, $address->getPostcode());
                $content .= $separator;
                /* city */
                $content = $this->_addFieldToCsv($content, $delimiter, $address->getCity());
                $content .= $separator;
                /* country code */
                $content = $this->_addFieldToCsv($content, $delimiter, $address->getCountry());
                $content .= $separator;
                /* telephone */
                $content = $this->_addFieldToCsv($content, $delimiter, $address->getTelephone());
                $content .= $separator;
                /* total weight */
                //$total_weight = 0;
                //$items = $order->getAllItems();
                //foreach ($items as $item) {
                //    $total_weight += $item['row_weight'];
                //}
                $content = $this->_addFieldToCsv($content, $delimiter, $order->getWeight());
                $content .= $lineBreak;
            }

            /* decode the content, depending on the charset */
            if ($fileCharset == 'ISO-8859-1') {
            	$content = utf8_decode($content);
            }

            /* pick file mime type, depending on the extension */
            if ($fileExtension == '.txt') {
            	$fileMimeType = 'text/plain';
            } else if ($fileExtension == '.csv') {
            	$fileMimeType = 'application/csv';
            } else {
            	// default
                $fileMimeType = 'text/plain';
            }

            /* download the file */
            return $this->_prepareDownloadResponse($filename, $content, $fileMimeType .'; charset="'. $fileCharset .'"');
        }
        else {
	        $this->_getSession()->addError($this->__('No Order has been selected'));
        }
    }

    /**
     * Add a new field to the csv file
     * @param csvContent : the current csv content
     * @param fieldDelimiter : the delimiter character
     * @param fieldContent : the content to add
     * @return : the concatenation of current content and content to add
     */
    private function _addFieldToCsv($csvContent, $fieldDelimiter, $fieldContent) {
	    return $csvContent . $fieldDelimiter . $fieldContent . $fieldDelimiter;
    }

}
