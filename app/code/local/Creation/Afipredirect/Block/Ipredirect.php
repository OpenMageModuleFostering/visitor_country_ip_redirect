<?php
/**
 * @category    Creation
 * @package     Creation_Afipredirect
 * @author 		Liudas Stanevicius <liudas@creation.lt>
 * @copyright 	Elsoft, UAB (www.creation.lt) <info@creation.lt>
 */

class Creation_Afipredirect_Block_Ipredirect extends Mage_Core_Block_Template
{
    protected function _construct()
    {
        parent::_construct();
		$this->_perform_redirect();
    }
	
	public function _perform_redirect()
	{
		$helper = $this->helper('afipredirect');
		
		if($helper->isEnabled() && $helper->isRedirectEnabled() && !$helper->isTestMode())
		{ 
			if($helper->userNeedsToBeRedirected())
			{
				$this->doTheRedirect();
			}
			
		}
	}
	
	private function doTheRedirect()
	{
		$helper = $this->helper('afipredirect');
		$destination_url = $helper->getDestinationWebsite();

		if($helper->isLogRedirects())
		{
			Mage::log($helper->getVisitorsIp().' ('.$helper->getVisitorsIpCountrySession().') redirected from '.Mage::helper('core/url')->getCurrentUrl().' to '.$helper->getDestinationWebsite(),null,'afipredirect.log');
		}

		if($helper->isJsRedirect())
		{
			echo '<script>window.location.href="'.$destination_url.'";</script>';
		} else {
			Mage::app()->getFrontController()->getResponse()->setRedirect($destination_url);
		}
	}
	
	
	
	
	public function canShowTestPanel()
	{
		$helper = $this->helper('afipredirect');
		if($helper->isEnabled() && $helper->isTestMode())
		{
			return true;
		} else {
			return false;
		}
	}
	
	
	
	
	
}