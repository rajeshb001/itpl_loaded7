<?php
/**
  @package    catalog::admin::applications
  @author     Loaded Commerce
  @copyright  Copyright 2003-2014 Loaded Commerce, LLC
  @copyright  Portions Copyright 2003 osCommerce
  @copyright  Template built on Developr theme by DisplayInline http://themeforest.net/user/displayinline under Extended license 
  @license    https://github.com/loadedcommerce/loaded7/blob/master/LICENSE.txt
  @version    $Id: restrict_shipping.php v1.0 2013-08-08 datazen $
*/
global $lC_Vqmod;

require_once($lC_Vqmod->modCheck('includes/applications/restrict_shipping/classes/restrict_shipping.php'));

class lC_Application_Restrict_shipping extends lC_Template_Admin {
 /*
  * Public variables
  */
  var $image_extension;
 /*
  * Protected variables
  */
  protected $_module = 'restrict_shipping',
            $_page_title,
            $_page_contents = 'main.php';
 /*
  * Class constructor
  */
  function __construct() {
    global $lC_Language, $lC_MessageStack;

    $this->_page_title = $lC_Language->get('heading_title');

    if ( !isset($_GET['action']) ) {
      $_GET['action'] = '';
    }

    // check if the graphs directory exists
    $_SESSION['errArr'] = array();
    

    if ( !empty($_GET['action']) && !($_SESSION['error']) ) {
      switch ( $_GET['action'] ) {	  
         case 'save':
			//echo '<pre>';print_r($_POST);exit;
			$shipping_name = serialize($_POST['ups']);
           $data = array('title' => $_POST['title'],
                         'shipping_name' => $shipping_name);
         /*
          * Save the banner information
          *
          * @param integer $_GET['bid'] The banner id
          * @param array $data The banner information
          * @access public
          * @return boolean
          */           
          if ( lC_Restrict_shipping_Admin::save((isset($_GET['bid']) && is_numeric($_GET['bid']) ? $_GET['bid'] : null), $data) ) {
            lc_redirect_admin(lc_href_link_admin(FILENAME_DEFAULT, $this->_module));
          } else {
            $_SESSION['error'] = true;
            $_SESSION['errmsg'] = $lC_Language->get('ms_error_action_not_performed');
          }
          break;
      }
    }
  }
}
?>