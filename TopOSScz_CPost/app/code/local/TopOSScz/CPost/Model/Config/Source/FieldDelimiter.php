<?php
/**
 * TopOSScz CPost module
 *
 * @category   TopOSScz
 * @package    TopOSScz_CPost
 * @copyright  Copyright (c) 2011 TopOSS.cz (Josef Jezek) <magento@toposs.cz>
 * @license    http://www.opensource.org/licenses/gpl-3.0.html GNU General Public License version 3
 */
class TopOSScz_CPost_Model_Config_Source_FieldDelimiter
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'simple_quote', 'label'=>Mage::helper('toposscz_cpost')->__('Simple Quote')),
            array('value'=>'double_quotes', 'label'=>Mage::helper('toposscz_cpost')->__('Double Quotes'))
        );
    }
}
