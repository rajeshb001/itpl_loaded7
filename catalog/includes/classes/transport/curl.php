<?php
/**
  @package    catalog::classes
  @author     Loaded Commerce
  @copyright  Copyright 2003-2014 Loaded Commerce, LLC
  @copyright  Portions Copyright 2003 osCommerce
  @license    https://github.com/loadedcommerce/loaded7/blob/master/LICENSE.txt
  @version    $Id: curl.php v1.0 2013-08-08 datazen $
*/
if (!class_exists('curl')) {
  class curl {
    /**
    * Curl Transport 
    *  
    * @param array    $parameters The parameters to process
    * @access public      
    * @return mixed
    */  
    public static function execute($parameters) {
      
      $request_type = getRequestType();      
      
      $curl = curl_init($request_type . '://' . $parameters['server']['host'] . $parameters['server']['path'] . (isset($parameters['server']['query']) ? '?' . $parameters['server']['query'] : ''));

      $curl_options = array(CURLOPT_PORT => $parameters['server']['port'],
                            CURLOPT_HEADER => true,
                            CURLOPT_SSL_VERIFYPEER => false,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_FORBID_REUSE => true,
                            CURLOPT_FRESH_CONNECT => true,
                            CURLOPT_FOLLOWLOCATION => false);

      if ( !empty($parameters['timeout']) ) {
        $curl_options[CURLOPT_TIMEOUT] = $parameters['timeout'];
      }
      
      if ( !empty($parameters['header']) ) {
        $curl_options[CURLOPT_HTTPHEADER] = $parameters['header'];
      }      

      if ( !empty($parameters['certificate']) ) {
        $curl_options[CURLOPT_SSLCERT] = $parameters['certificate'];
      }

      if ( $parameters['method'] == 'post' ) {
        $curl_options[CURLOPT_POST] = true;
        $curl_options[CURLOPT_POSTFIELDS] = $parameters['parameters'];
      }
      
     // added support for curl proxy
      if (defined('CURL_PROXY_HOST') && defined('CURL_PROXY_PORT') && CURL_PROXY_HOST != NULL && CURL_PROXY_PORT != NULL) {
        $curl_options[CURLOPT_HTTPPROXYTUNNEL] = true;
        $curl_options[CURLOPT_PROXY] = CURL_PROXY_HOST . ":" . CURL_PROXY_PORT;
      }
      if (defined('CURL_PROXY_USER') && defined('CURL_PROXY_PASSWORD') && CURL_PROXY_USER != NULL && CURL_PROXY_PASSWORD != NULL) {
        $curl_options[CURLOPT_PROXYUSERPWD] = CURL_PROXY_USER . ':' . CURL_PROXY_PASSWORD;
      }    

      curl_setopt_array($curl, $curl_options);
      $result = curl_exec($curl);

      $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
      $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

      curl_close($curl);
      
      if ($parameters['rawResponse'] === true) {
        $body = $result;
      } else {
        list($headers, $body) = explode("\r\n\r\n", $result, 2);
      }
      
      if  ($http_code > 299)  {  // there was some sort of error
        // log the error
        self::log('cURL Error: (' . $http_code . ') requesting ' . $parameters['url']);
      }      

      return $body;
    }
    /**
    * Is Curl available 
    *  
    * @access public      
    * @return boolean
    */ 
    public static function canUse() {
      return function_exists('curl_init');
    }
   /**
    * Make a log entry
    *  
    * @param string $message  The message to log
    * @access protected      
    * @return void
    */ 
    protected static function log($message) {
      if ( is_writable(DIR_FS_WORK . 'logs') ) {
        file_put_contents(DIR_FS_WORK . 'logs/curl_errors.txt', '[' . lC_DateTime::getNow('d-M-Y H:i:s') . '] ' . $message . "\n", FILE_APPEND);
      }
    }    
  }
}
?>