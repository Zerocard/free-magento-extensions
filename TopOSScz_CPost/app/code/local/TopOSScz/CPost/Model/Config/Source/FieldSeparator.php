<?php
/**
 * TopOSScz CPost module
 *
 * @category   TopOSScz
 * @package    TopOSScz_CPost
 * @copyright  Copyright (c) 2011 TopOSS.cz (Josef Jezek) <magento@toposs.cz>
 * @license    http://www.opensource.org/licenses/gpl-3.0.html GNU General Public License version 3
 */
class TopOSScz_CPost_Model_Config_Source_FieldSeparator
{
    public function toOptionArray()
    {
        return array(
            array('value'=>';', 'label'=>Mage::helper('toposscz_cpost')->__(';')),
            array('value'=>',', 'label'=>Mage::helper('toposscz_cpost')->__(','))
        );
    }
}
