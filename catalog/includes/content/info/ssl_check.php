<?php
/**
  @package    catalog::content
  @author     Loaded Commerce
  @copyright  Copyright 2003-2014 Loaded Commerce, LLC
  @copyright  Portions Copyright 2003 osCommerce
  @license    https://github.com/loadedcommerce/loaded7/blob/master/LICENSE.txt
  @version    $Id: ssl_check.php v1.0 2013-08-08 datazen $
*/
class lC_Info_Ssl_check extends lC_Template {

  /* Private variables */
  var $_module = 'ssl_check',
      $_group = 'info',
      $_page_title,
      $_page_contents = 'ssl_check.php',
      $_page_image = 'table_background_specials.gif';

  /* Class constructor */
  public function lC_Info_Ssl_check() {
    global $lC_Services, $lC_Language, $lC_Breadcrumb;

    $this->_page_title = $lC_Language->get('info_ssl_check_heading');

    if ($lC_Services->isStarted('breadcrumb')) {
      $lC_Breadcrumb->add($lC_Language->get('breadcrumb_ssl_check'), lc_href_link(FILENAME_INFO, $this->_module));
    }
  }
}
?>