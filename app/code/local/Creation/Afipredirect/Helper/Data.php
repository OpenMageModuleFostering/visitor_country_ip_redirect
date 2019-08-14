<?php

/**
 * @category    Creation
 * @package     Creation_Afipredirect
 * @author 		Liudas Stanevicius <liudas@creation.lt>
 * @copyright 	Elsoft, UAB (www.creation.lt) <info@creation.lt>
 */

class Creation_Afipredirect_Helper_Data extends Mage_Core_Helper_Abstract
{

    const XML_PATH_ENABLED          				= 'afipredirect/settings/enable';
    const XML_PATH_SETTINGS_JSREDIRECT  			= 'afipredirect/settings/jsredirect';
    const XML_PATH_SETTINGS_IS_TEST_MODE    		= 'afipredirect/settings/is_test_mode';
    const XML_PATH_SETTINGS_TEST_MODE_IP    		= 'afipredirect/settings/test_mode_ip';
    const XML_PATH_SETTINGS_LOOKUP_SERVICE    		= 'afipredirect/settings/country_lookup_service';
    const XML_PATH_SETTINGS_EXCLUDE_IPS    			= 'afipredirect/settings/exlude_ips';
	const XML_PATH_SETTINGS_EXCLUDE_SEARCH_ENGINES	= 'afipredirect/settings/exlude_search_engines';
	const XML_PATH_LOG_REDIRECTS_ENABLED		 	= 'afipredirect/settings/log_redirects';
	
    const XML_PATH_REDIRECT_SOURCE_COUNTRY  		= 'afipredirect/redirect{{number}}/source_country';
    const XML_PATH_REDIRECT_DESTINATION_WEBSITE   	= 'afipredirect/redirect{{number}}/destination_website';
	const XML_PATH_REDIRECT_ENABLED		 			= 'afipredirect/redirect{{number}}/redirect_enabled';
	const XML_PATH_REDIRECT_ONCE_ENABLED		 	= 'afipredirect/redirect{{number}}/redirect_once';
	const XML_PATH_REDIRECT_LAND_URL			 	= 'afipredirect/redirect{{number}}/redirect_land_url';
	
	const NUMBER_CONST			 					= '{{number}}';

    

	public function getRedirectSettings($path, $number)
	{
		if(($number == false) || ($number == 1))
		{
			// since 1st redirect does not have numbering in the path..
			$number = '';
		}
		
		$result = str_replace(self::NUMBER_CONST,$number,$path);
		return $result;
	}

    public function isEnabled()
    {
		return Mage::getStoreConfig( self::XML_PATH_ENABLED );
    }
    public function isRedirectOnce($redirect_no = false)
    {
		$path = $this->getRedirectSettings(self::XML_PATH_REDIRECT_ONCE_ENABLED,$redirect_no); 
        return Mage::getStoreConfig( $path );
    }
	
    public function isRedirectLandUrl($redirect_no = false)
    {
		$path = $this->getRedirectSettings(self::XML_PATH_REDIRECT_LAND_URL,$redirect_no); 
        return Mage::getStoreConfig( $path );
    }
	
    public function isLogRedirects()
    {
        return Mage::getStoreConfig( self::XML_PATH_LOG_REDIRECTS_ENABLED );
    }
	
    public function isRedirectEnabled($redirect_no = false)
    {
		$path = $this->getRedirectSettings(self::XML_PATH_REDIRECT_ENABLED,$redirect_no); 
        return Mage::getStoreConfig( $path );
    }	
	
    public function isTestMode()
    {
        return Mage::getStoreConfig( self::XML_PATH_SETTINGS_IS_TEST_MODE );
    }	
	
    public function getExcludedIps()
    {
        return Mage::getStoreConfig( self::XML_PATH_SETTINGS_EXCLUDE_IPS );
    }
    public function getTestModeIp()
    {
        return Mage::getStoreConfig( self::XML_PATH_SETTINGS_TEST_MODE_IP );
    }
	
    public function getCountryLookupService()
    {
        return Mage::getStoreConfig( self::XML_PATH_SETTINGS_LOOKUP_SERVICE );
    }
	
    public function getSourceCountry($redirect_no = false)
    {
		$path = $this->getRedirectSettings(self::XML_PATH_REDIRECT_SOURCE_COUNTRY,$redirect_no); 
        return Mage::getStoreConfig( $path );
        //return Mage::getStoreConfig( self::XML_PATH_REDIRECT_SOURCE_COUNTRY );
    }
	
    public function getDestinationWebsite($redirect_no = false)
    {
        $path = $this->getRedirectSettings(self::XML_PATH_REDIRECT_DESTINATION_WEBSITE,$redirect_no); 
        return Mage::getStoreConfig( $path );
		//return Mage::getStoreConfig( self::XML_PATH_REDIRECT_DESTINATION_WEBSITE );
    }
    public function isJsRedirect()
    {
        return Mage::getStoreConfig( self::XML_PATH_SETTINGS_JSREDIRECT );
    }
    public function getExcludedSearchEngineBots()
    {
        return Mage::getStoreConfig( self::XML_PATH_SETTINGS_EXCLUDE_SEARCH_ENGINES );
    }

	
	public function getVisitorsIp()
	{
		return Mage::helper('core/http')->getRemoteAddr();
	}
	
	
	
	public function getIpToTest()
	{
		$test_ip = $this->getTestModeIp();
		if(filter_var($test_ip, FILTER_VALIDATE_IP))
		{
			return $test_ip;
		} else {
			return $this->getVisitorsIp();
		}
	}
	
	public function getVisitorsIpCountrySession()
	{
		$session = Mage::getSingleton("core/session",  array("name"=>"frontend"));
		return $session->getData($this->getCountryLookupService()."_visitor_ip_country");
	}
	
	
	public function setVisitorsIpCountrySession($country_code)
	{
		$session = Mage::getSingleton("core/session",  array("name"=>"frontend"));
		$session->setData($this->getCountryLookupService()."_visitor_ip_country", $country_code);
		
	}
	
	
	public function isSearchEngineBot()
	{
		$bots = array(
			'Googlebot', 'Baiduspider', 'ia_archiver',
			'R6_FeedFetcher', 'NetcraftSurveyAgent', 'Sogou web spider',
			'bingbot', 'Yahoo! Slurp', 'facebookexternalhit', 'PrintfulBot',
			'msnbot', 'Twitterbot', 'UnwindFetchor',
			'urlresolver', 'Butterfly', 'TweetmemeBot' );
	 
		foreach($bots as $b){
	 
			if( stripos( Mage::helper('core/http')->getHttpUserAgent(), $b ) !== false ) return true;
	 
		}
		return false;
	}
	
	
	public function excludeThisVisitorFromRedirect($ip_address = false)
	{
		if($ip_address == false)
		{
			$ip_address = $this->getVisitorsIp();
		}
		
		$excluded_ips = $this->getExcludedIps();
		
		if($excluded_ips != '')
		{
			$excluded_ips = explode(',',$excluded_ips);
			
			$excluded_ips_clean = array();
			foreach($excluded_ips as $ip)
			{
				$excluded_ips_clean[] = trim($ip);
			}
			
			if((count($excluded_ips_clean) > 0) && in_array($ip_address,$excluded_ips_clean))
			{
				return true;
			}
		} 
		return false;
	}
	
	
	public function userNeedsToBeRedirected($number)
	{
		$visitors_ip = $this->getVisitorsIp();
		// is this visitor in the IP exlude list?
		if($this->excludeThisVisitorFromRedirect($visitors_ip))
		{
			return false;
		}
	
		// check if this is not a search engine bot
		if($this->getExcludedSearchEngineBots() && $this->isSearchEngineBot())
		{
			return false;
		}
	
		// do we redirect based on the landing url
		if(!$this->landAndCurrentUrlMatch($number))
		{
			return false;
		}

		// has the visitor been here before?
		$country_code = $this->getVisitorsIpCountrySession();

		// if not, lookup country code and store in the session
		if($country_code == NULL)
		{
			$country_code = $this->getCountryCodeByIp($visitors_ip);
			$this->setVisitorsIpCountrySession($country_code);
		} elseif($this->isRedirectOnce($number))
		{
			// the user has been redirected, maybe thats enough?
			return false;
		}
	
		
		// get config values
		$source_countries = $this->getSourceCountry($number);
		$source_countries = explode(',',$source_countries);
		
		// match?
		if(in_array($country_code,$source_countries))
		{
			return true;
		} else {
			return false;
		}
	}

	public function landAndCurrentUrlMatch($number = false)
	{
		$landing_url = rtrim($this->isRedirectLandUrl($number),'/');
		if($landing_url == '')
		{
			// no landign URL is defined - continue conditions check;
			return true;
		}
		$current_url = rtrim(Mage::helper('core/url')->getCurrentUrl(),'/');
		
		$parse = parse_url($landing_url);
		if(!isset($parse['path']))
		{
			$parse['path'] = '';
		}
		$landing_url_clean = preg_replace('#^www\.(.+\.)#i', '$1', $parse['host']) . $parse['path'];
		
		$parse = parse_url($current_url);
		if(!isset($parse['path']))
		{
			$parse['path'] = '';
		}
		$current_url_clean = preg_replace('#^www\.(.+\.)#i', '$1', $parse['host']) . $parse['path'];
		
		if($landing_url_clean == $current_url_clean)
		{
			return true;
		} else {
			return false;
		}
	
	
	}
	
	public function getRedirectResultByIp($ip_address = false, $number = false)
	{
		if($ip_address == false)
		{
			$ip_address = $this->getVisitorsIp();
		}
	
		$country_code = $this->getCountryCodeByIp($ip_address);
		$source_countries = $this->getSourceCountry($number);
		
		$source_countries = explode(',',$source_countries);
		
		
		if(in_array($country_code,$source_countries))
		{
			// do we redirect based on the landing url
			if(!$this->landAndCurrentUrlMatch($number))
			{
				return false;
			} else {
				return true;
			}
		} else {
			return false;
		}
	}
	
	public function getIpTestResults($ip_address)
	{
		$country_code = $this->getCountryCodeByIp($ip_address);
		if($country_code != false)
		{
			$country_name = Mage::app()->getLocale()->getCountryTranslation($country_code);
			if($country_name != false)
			{
				return $country_name.' ('.$country_code.')';
			} else {
				return 'Unknown ('.$country_code.')';
			}
		} else {
			return 'Unknown';
		}
	}
	
	
	public function getCountryCodeByIp($ip_address = false)
	{
		if($ip_address == false)
		{
			$ip_address = $this->getVisitorsIp();
		}
		
		$lookup_service = $this->getCountryLookupService();
		
		$start = microtime(true);
		switch ($lookup_service) {
			case 'geoplugin':
				$result = $this->geoplugin_visitor_country($ip_address);
				break;
			case 'hostip':
				$result = $this->hostip_visitor_country($ip_address);
				break;
			case 'iptolatlng':
				$result = $this->iptolatlng_visitor_country($ip_address);
				break;
			default:
				$result = $this->iptolatlng_visitor_country($ip_address);
		}
		$end = microtime(true);
		$duration = (string) $end - $start;
		if($this->isLogRedirects())
		{
			Mage::log($lookup_service.' returned country code '.$result.' on IP address '.$this->getVisitorsIp().' in '.$duration.' seconds' ,null,'afipredirect.log');
		}
		return $result;
		
	}
	
	
	
	private function geoplugin_visitor_country($ip_address = false)
	{
		
		if($ip_address == false)
		{
			$ip_address = $this->getVisitorsIp();
		}
		
		$result  = false;
		
		$ip_data = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".$ip_address));
		if($ip_data && $ip_data->geoplugin_countryCode != null)
		{
			$result = $ip_data->geoplugin_countryCode;
		}
		return $result;
	}	
	
	private function hostip_visitor_country($ip_address = false)
	{
		
		if($ip_address == false)
		{
			$ip_address = $this->getVisitorsIp();
		}
		
		$result  = false;
		
		$ip_data = @file_get_contents("http://api.hostip.info/country.php?ip=".$ip_address);

		if($ip_data && $ip_data != null)
		{
			$result = $ip_data;
		}
		
		return $result;
	}	
	
	private function iptolatlng_visitor_country($ip_address = false)
	{
		if($ip_address == false)
		{
			$ip_address = $this->getVisitorsIp();
		}
		
		$result  = false;

		$ip_data = @json_decode(file_get_contents("http://www.iptolatlng.com?ip=".$ip_address."&type=json"));

		if($ip_data && $ip_data->country != null)
		{
			$result = $ip_data->country;
		}
		
		return $result;
	}
	
	
	
}
?>