<?php
/**
 * TopOSScz CPost module
 *
 * @category   TopOSScz
 * @package    TopOSScz_CPost
 * @copyright  Copyright (c) 2009 Jibé <contact via Magento Connect module page>
 * @license    http://www.opensource.org/licenses/gpl-3.0.html GNU General Public License version 3
 */
class TopOSScz_CPost_ImportController extends Mage_Adminhtml_Controller_Action
{

    /**
     * Constructor
     */
    protected function _construct()
    {        
        $this->setUsedModuleName('TopOSScz_CPost');
    }

    /**
     * Main action : show import form
     */
    public function indexAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('sales/toposscz_cpost/import')
            ->_addContent($this->getLayout()->createBlock('toposscz_cpost/import_form'))
            ->renderLayout();
    }

    /**
     * Import Action
     */
    public function importAction()
    {
        if ($this->getRequest()->isPost() && !empty($_FILES['import_toposscz_cpost_file']['tmp_name'])) {
            try {
                $trackingTitle = $_POST['import_toposscz_cpost_tracking_title'];
                $this->_importTopOSSczCPostFile($_FILES['import_toposscz_cpost_file']['tmp_name'], $trackingTitle);
            }
            catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_getSession()->addError($this->__('Invalid file upload attempt'));
            }
        }
        else {
            $this->_getSession()->addError($this->__('Invalid file upload attempt'));
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Importation logic
     * @param string $fileName
     * @param string $trackingTitle
     */
    protected function _importTopOSSczCPostFile($fileName, $trackingTitle)
    {
        /**
         * File handling
         **/
        ini_set('auto_detect_line_endings', true);
        $csvObject = new Varien_File_Csv();
        $csvObject->setDelimiter(';');
        $csvData = $csvObject->getData($fileName);

        /**
         * File expected fields
         */
        $expectedCsvFields  = array(
            0   => $this->__('Order Id'),
            1   => $this->__('Tracking Number')
        );

        /**
         * Get configuration
         */
        $sendEmail = Mage::helper('toposscz_cpost')->getConfigurationSendEmail();
        $commentDraft = Mage::helper('toposscz_cpost')->getConfigurationShippingComment();
        $includeComment = Mage::helper('toposscz_cpost')->getConfigurationIncludeComment();

        /* debug */
        //$this->_getSession()->addSuccess($this->__('%s - %s - %s - %s', $sendEmail, $comment, $includeComment, $trackingTitle));

        /**
         * $k is line number
         * $v is line content array
         */
        foreach ($csvData as $k => $v) {

            /**
             * End of file has more than one empty lines
             */
            if (count($v) <= 1 && !strlen($v[0])) {
                continue;
            }

            /**
             * Check that the number of fields is not lower than expected
             */
            if (count($v) < count($expectedCsvFields)) {
                $this->_getSession()->addError($this->__('Line %s format is invalid and has been ignored', $k));
                continue;
            }

            /**
             * Get fields content
             */
            //$orderId = $v[0];
            $invoiceId = $v[22];
            $trackingNumber = $v[0];
	    $comment = $commentDraft.$trackingNumber;

            /* for debug */
            //$this->_getSession()->addSuccess($this->__('Lecture ligne %s: %s - %s', $k, $orderId, $trackingNumber));

            /**
             * Try to load the order
             */
            //$order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
	    $invoice = Mage::getModel('sales/order_invoice')->loadByIncrementId($invoiceId);
            if (!$invoice->getId()) {
                $this->_getSession()->addError($this->__('Invoice %s does not exist', $invoiceId));
                continue;
            }

	    $order = $invoice->getOrder();
	    //$orderId = $invoice->getOrderId();
            //$order = Mage::getModel('sales/order')->loadById($orderId);
	    
            /**
             * Try to create a shipment
             */
            $shipmentId = $this->_createShipment($order, $invoice, $trackingNumber, $trackingTitle, $sendEmail, $comment, $includeComment);
            
            if ($shipmentId != 0) {
                $this->_getSession()->addSuccess($this->__('Shipment %s created for order %s, with tracking number %s', $shipmentId, $orderId, $trackingNumber));
            }
             
        }//foreach

    }

    /**
     * Create new shipment for order
     * Inspired by Mage_Sales_Model_Order_Shipment_Api methods
     *
     * @param Mage_Sales_Model_Order $order (it should exist, no control is done into the method)
     * @param string $trackingNumber
     * @param string $trackingTitle
     * @param booleam $email
     * @param string $comment
     * @param boolean $includeComment
     * @return int : shipment real id if creation was ok, else 0
     */
    public function _createShipment($order, $invoice, $trackingNumber, $trackingTitle, $email, $comment, $includeComment)
    {
        /**
         * Check shipment creation availability
         */
        if (!$order->canShip()) {
            $this->_getSession()->addError($this->__('Order %s - Invoice %s can not be shipped or has already been shipped, tracking number %s', $order->getIncrementId(), $invoice->getIncrementId(), $trackingNumber));
            return 0;
        }

        /**
         * Initialize the Mage_Sales_Model_Order_Shipment object
         */
        $convertor = Mage::getModel('sales/convert_order');
        $shipment = $convertor->toShipment($order);

        /**
         * Add the items to send
         */
        foreach ($order->getAllItems() as $orderItem) {
            if (!$orderItem->getQtyToShip()) {
                continue;
            }
            if ($orderItem->getIsVirtual()) {
                continue;
            }

            $item = $convertor->itemToShipmentItem($orderItem);
            $qty = $orderItem->getQtyToShip();
            $item->setQty($qty);

        	$shipment->addItem($item);
        }//foreach

        $shipment->register();

        /**
         * Tracking number instanciation
         */
        $track = Mage::getModel('sales/order_shipment_track')
                	->setNumber($trackingNumber)
                    ->setCarrierCode('custom')
                    ->setTitle($trackingTitle);
        $shipment->addTrack($track);

        /**
         * Comment handling
         */
        $shipment->addComment($comment, $email && $includeComment);

        /**
         * Change order status to Processing
         */
        //$shipment->getOrder()->setIsInProcess(true);

	/**
	 * Change order status to Processing shipped
	 */
	$shipment->getOrder()->setState('processing', 'processing_shipped', $this->__('Tracking Number').': '.$trackingNumber, True);

        /**
         * If e-mail, set as sent (must be done before shipment object saving)
         */
        if ($email) {
            $shipment->setEmailSent(true);
        }

        try {
        	/**
             * Save the created shipment and the updated order
             */
            $shipment->save();
            $shipment->getOrder()->save();

            /**
             * Email sending
             */
            $shipment->sendEmail($email, ($includeComment ? $comment : ''));
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($this->__('Shipment creation error for Order %s : %s', $orderId, $e->getMessage()));
            return 0;
        }

        $this->_getSession()->addSuccess($this->__('Tracking number %s was successfully added to order %s', $trackingNumber, $order->getIncrementId()));

        /**
         * Everything was ok : return Shipment real id
         */
        return $shipment->getIncrementId();
    }

}
