<?php
/**
 * TopOSScz CPost module
 *
 * @category   TopOSScz
 * @package    TopOSScz_CPost
 * @copyright  Copyright (c) 2011 TopOSS.cz (Josef Jezek) <magento@toposs.cz>
 * @license    http://www.opensource.org/licenses/gpl-3.0.html GNU General Public License version 3
 */
class TopOSScz_CPost_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * Export configuration
     */
    public function getConfigurationFileExtension() {
        return Mage::getStoreConfig('toposscz_cpost/export/file_extension');
    }

    public function getConfigurationFileCharset() {
        return Mage::getStoreConfig('toposscz_cpost/export/file_charset');
    }

    public function getConfigurationEndOfLineCharacter() {
        return Mage::getStoreConfig('toposscz_cpost/export/endofline_character');
    }

    public function getConfigurationFieldDelimiter() {
        return Mage::getStoreConfig('toposscz_cpost/export/field_delimiter');
    }

    public function getConfigurationFieldSeparator() {
        return Mage::getStoreConfig('toposscz_cpost/export/field_separator');
    }

    /**
     * Import configuration
     */
    public function getConfigurationSendEmail() {
        return Mage::getStoreConfig('toposscz_cpost/import/send_email');
    }

    public function getConfigurationIncludeComment() {
        return Mage::getStoreConfig('toposscz_cpost/import/include_comment');
    }

    public function getConfigurationDefaultTrackingTitle() {
        return Mage::getStoreConfig('toposscz_cpost/import/default_tracking_title');
    }

    public function getConfigurationShippingComment() {
        return Mage::getStoreConfig('toposscz_cpost/import/shipping_comment');
    }

}
