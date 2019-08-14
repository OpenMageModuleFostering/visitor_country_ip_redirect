<?php
/**
 * @category    Creation
 * @package     Creation_Afipredirect
 * @author 		Liudas Stanevicius <liudas@creation.lt>
 * @copyright 	Elsoft, UAB (www.creation.lt) <info@creation.lt>
 */

class Creation_Afipredirect_Adminhtml_Model_System_Config_Source_Countrylookupproviders extends Mage_Core_Model_Config_Data
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'geoplugin', 'label'=>Mage::helper('adminhtml')->__('geoplugin.net')),
            array('value' => 'hostip', 'label'=>Mage::helper('adminhtml')->__('hostip.info')),
            array('value' => 'iptolatlng', 'label'=>Mage::helper('adminhtml')->__('iptolatlng.com')),
        );
    }
	
	public function save()
	{
		$service_provider = $this->getValue();
		
		if($service_provider != Mage::getStoreConfig( 'afipredirect/settings/country_lookup_service' ))
		{
			Mage::getSingleton('core/session')->addNotice ('You have changed the Country lookup service provider. Have in mind, that different providers could return different results.');
		}

		return parent::save();
	}

}