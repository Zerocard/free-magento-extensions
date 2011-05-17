<?php
/**
 * TopOSScz CPost module
 *
 * @category   TopOSScz
 * @package    TopOSScz_CPost
 * @copyright  Copyright (c) 2011 TopOSS.cz (Josef Jezek) <magento@toposs.cz>
 * @license    http://www.opensource.org/licenses/gpl-3.0.html GNU General Public License version 3
 */
class TopOSScz_CPost_Model_Config_Source_EndOfLineCharacter
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'lf', 'label'=>Mage::helper('toposscz_cpost')->__('LF')),
            array('value'=>'cr', 'label'=>Mage::helper('toposscz_cpost')->__('CR')),
            array('value'=>'crlf', 'label'=>Mage::helper('toposscz_cpost')->__('CR+LF'))
        );
    }
}
