<?php
/**
  @package    catalog::admin::applications
  @author     Loaded Commerce
  @copyright  Copyright 2003-2014 Loaded Commerce, LLC
  @copyright  Portions Copyright 2003 osCommerce
  @copyright  Template built on Developr theme by DisplayInline http://themeforest.net/user/displayinline under Extended license 
  @license    https://github.com/loadedcommerce/loaded7/blob/master/LICENSE.txt
  @version    $Id: languages.php v1.0 2013-08-08 datazen $
*/
global $lC_Vqmod;

require_once($lC_Vqmod->modCheck('includes/applications/languages/classes/languages.php'));
require_once($lC_Vqmod->modCheck('includes/applications/currencies/classes/currencies.php'));

class lC_Application_Languages extends lC_Template_Admin {
  /*
  * Protected variables
  */
  protected $_module = 'languages',
            $_page_title,
            $_page_contents = 'main.php';
  /*
  * Class constructor
  */
  public function __construct() {
    global $lC_Language, $breadcrumb_string;

    $this->_page_title = $lC_Language->get('heading_title');

    $breadcrumb_array = array(lc_link_object(lc_href_link_admin(FILENAME_DEFAULT, $this->_module), $lC_Language->get('heading_title')));   
    if ( !empty($_GET[$this->_module]) && is_numeric($_GET[$this->_module]) && lC_Languages_Admin::exists($_GET[$this->_module]) ) {
      $this->_page_title = lC_Languages_Admin::get($_GET[$this->_module], 'name');
      $this->_page_contents = 'groups.php';
      $breadcrumb_array[] = lc_link_object(lc_href_link_admin(FILENAME_DEFAULT, $this->_module . '=' . $this->_page_contents ), $this->_page_title);

      if ( isset($_GET['group']) && !empty($_GET['group']) && lC_Languages_Admin::isDefinitionGroup($_GET[$this->_module], $_GET['group']) ) {
        $this->_page_title = $_GET['group'];
        $this->_page_contents = 'definitions.php';
        $breadcrumb_array[] = lc_link_object(lc_href_link_admin(FILENAME_DEFAULT, $this->_module . '=' . $this->_page_contents ), $this->_page_title);
      }
    }
    $breadcrumb_string = '<ul>';
    foreach ($breadcrumb_array as $key => $value) {
      $breadcrumb_string .= '<li>' . lc_output_utf8_decoded($value) . '</li>';
    }  
    $breadcrumb_string .= '</ul>';     

    if ( !isset($_GET['action']) ) {
      $_GET['action'] = '';
    }

    if ( !empty($_GET['action']) ) {
      switch ( $_GET['action'] ) {
        case 'import':
          $lang = (isset($_GET['language_import']) && $_GET['language_import'] != NULL) ? $_GET['language_import'] : $_POST['language_import'];
          $type = (isset($_GET['import_type']) && $_GET['import_type'] != NULL) ? $_GET['import_type'] : $_POST['import_type'];
          
          try {
            lC_Languages_Admin::import($lang, $type);
           } catch ( Exception $e ) {

            $_SESSION['error'] = true;
            $_SESSION['errmsg'] = $lC_Language->get('ms_error_action_not_performed');
          }

          break;

        case 'export':
          lC_Languages_Admin::export($_GET['lid'], $_POST['groups'], (isset($_POST['include_data']) && ($_POST['include_data'] == 'on')));
          break;
      }
    }
  }
}
?>