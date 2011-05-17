<?php
/**
 * TopOSScz CPost module
 *
 * @category   TopOSScz
 * @package    TopOSScz_CPost
 * @copyright  Copyright (c) 2011 TopOSS.cz (Josef Jezek) <magento@toposs.cz>
 * @license    http://www.opensource.org/licenses/gpl-3.0.html GNU General Public License version 3
 */
class TopOSScz_CPost_Model_Config_Source_FileCharset
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'ISO-8859-1', 'label'=>Mage::helper('toposscz_cpost')->__('ISO-8859-1')),
            array('value'=>'UTF-8', 'label'=>Mage::helper('toposscz_cpost')->__('UTF-8'))
        );
    }
}
