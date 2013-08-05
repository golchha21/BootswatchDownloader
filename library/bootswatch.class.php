<?php
	error_reporting(E_ERROR);
	define('_CODENAME', 'BootswatchDownloader'); 
	define('_VERSION', '1.0.1'); 
	define('_URL', 'https://github.com/golchha21/BootswatchDownloader');
	define('DS', DIRECTORY_SEPARATOR);
	define('ROOT', dirname('../'));
	set_time_limit(0);
	
	class BOOTSWATCH{
	
		private $timeout 	= 300; 								// CURL timeout
		private $redirects 	= 5; 								// CURL max redirects
		private $useragent 	= 'BootswatchDownloader v 1.0.0'; 	// CURL Useragent
		private $folder 	= 'themes';							// Themes folder
		private $vvise	 	= true;								// Save bootswatch files in version sub-folder
		
		// Constructor
		function __construct($args = false){
			if(is_array($args)){
				$this->redirects	= (isset($redirects) && $redirects > $this->redirects ? $redirects : $this->redirects);
				$this->timeout		= (isset($timeout) && $timeout > $this->timeout ? $timeout : $this->timeout);
				$this->useragent	= (isset($useragent) && !empty($useragent) ? $useragent : $this->useragent);
				$this->folder		= (isset($folder) && !empty($folder) ? $folder : $this->folder);
				$this->version		= (isset($version) && !empty($version) ? $version : $this->version);
			}
			return $this->themes();
		}
			
		function themes(){
		
			if(!is_dir($this->folder)){
				mkdir($this->folder);
			}
			
			$api_url = 'http://api.bootswatch.com/';
			$data = $this->getDataFromUrl($api_url);
			$data = json_decode($data, true);
			extract($data);
			
			if($this->vvise){
				$this->folder = $this->folder.DS.$version;
				
				if(!is_dir($this->folder)){
					mkdir($this->folder);
				}
			}
			
			foreach($themes as $theme){
				extract($theme);
				$dn = $this->folder.DS.$name.DS;
				if(!is_dir($dn)){
					mkdir($dn);
				}
				$dn = ROOT.DS.$this->folder.DS.$name.DS;
				$this->put_data($this->getDataFromUrl($thumbnail), $dn.'thumbnail.png');
				$this->put_data($this->getDataFromUrl($css), $dn.'bootstrap.css');
				$this->put_data($this->getDataFromUrl($cssMin), $dn.'bootstrap.min.css');
				$this->put_data($this->getDataFromUrl($less), $dn.'bootswatch.less');
				$this->put_data($this->getDataFromUrl($lessVariables), $dn.'variables.less');
			}

			return true;
		}
		
		// Gets the content from the URL
		function getDataFromUrl($url){
			$return = false;
			if (extension_loaded('curl')){
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_HEADER, false);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_AUTOREFERER, true);
				curl_setopt($ch, CURLOPT_MAXREDIRS, $this->redirects);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
				curl_setopt($ch, CURLOPT_CERTINFO, true);
				curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
				$return	= curl_exec($ch);
				curl_close($ch);
			}elseif(ini_get('allow_url_fopen')){
				$return = file_get_contents($url);
			}
			return $return;
		}
		
		function put_data($data, $filename){
			$fp = fopen($filename, 'w') or die("can't open file - " . $filename);
			fwrite($fp, $data);
			fclose($fp);
		}
	}
?>