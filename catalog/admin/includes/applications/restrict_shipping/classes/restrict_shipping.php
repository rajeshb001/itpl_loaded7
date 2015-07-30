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
class lC_Restrict_shipping_Admin {
 /*
  * Returns the banners datatable data for listings
  *
  * @access public
  * @return array
  */
  public static function getAll() {
    global $lC_Database, $lC_Language, $_module;
    
    $media = $_GET['media'];

    $Qrestrict = $lC_Database->query('select * from :table_shipping_restrict order by title');
    $Qrestrict->bindTable(':table_shipping_restrict', TABLE_SHIPPING_RESTRICT);
    $Qrestrict->setBatchLimit($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS);
    $Qrestrict->execute();

    $result = array('aaData' => array());

    while ( $Qrestrict->next() ) {
      $check = '<td><input class="batch" type="checkbox" name="batch[]" value="' . $Qrestrict->valueInt('rule_id') . '" id="' . $Qrestrict->valueInt('rule_id') . '"></td>';
      $rules = '<td><span class="with-tooltip" ></span>&nbsp;' . $Qrestrict->value('title') . '</span></td>';  
     
      $action = '<td class="align-right vertical-center">
                   <span class="button-group">
                     <a href="' . ((int)($_SESSION['admin']['access'][$_module] < 3) ? '#' : 'javascript://" onclick="editBanner(\'' . $Qrestrict->valueInt('rule_id') . '\')') . '" class="button icon-pencil' . ((int)($_SESSION['admin']['access'][$_module] < 3) ? ' disabled' : NULL) . '">' . (($media === 'mobile-portrait' || $media === 'mobile-landscape') ? NULL : $lC_Language->get('icon_edit')) . '</a>                     
                   </span>
                   <span class="button-group">
                     <a href="' . ((int)($_SESSION['admin']['access'][$_module] < 4) ? '#' : 'javascript://" onclick="deleteBanner(\'' . $Qrestrict->valueInt('rule_id') . '\', \'' . urlencode($Qrestrict->value('title')) . '\')') . '" class="button icon-trash with-tooltip' . ((int)($_SESSION['admin']['access'][$_module] < 4) ? ' disabled' : NULL) . '" title="' . $lC_Language->get('icon_delete') . '"></a>
                   </span>
                 </td>';
                
      $result['aaData'][] = array("$check", "$rules", "$action");
      $result['entries'][] = $Qrestrict->toArray();
    }

    return $result;
  }
 /*
  * Return the data used on the dialog forms
  *
  * @param integer $id The banner id
  * @access public
  * @return array
  */
  public static function formData($id = '') {
    global $lC_Database;

    $result = array();
    $Qgroups = $lC_Database->query('select distinct banners_group from :table_banners order by banners_group');
    $Qgroups->bindTable(':table_banners', TABLE_BANNERS);
    $Qgroups->execute();
    $groups_array = array();
    while ( $Qgroups->next() ) {
      $groups_array[$Qgroups->value('banners_group')] = $Qgroups->value('banners_group');
    }
    $result['groupsArray'] = $groups_array;

    if ($id != null) {
      $result['bannerData'] = lC_Restrict_shipping_Admin::getData($id); 
    }

    return $result;
  }
 /*
  * Return banner information
  *
  * @param integer $id The banner id
  * @access public
  * @return array
  */
  public static function getData($id) {
    global $lC_Database;

    $Qbanner = $lC_Database->query('select * from :shipping_restrict where rule_id = :rule_id');
    $Qbanner->bindTable(':shipping_restrict', TABLE_SHIPPING_RESTRICT);
    $Qbanner->bindInt(':rule_id', $id);
    $Qbanner->execute();

    $data = $Qbanner->toArray();
    $Qbanner->freeResult();

    return $data;
  } 

 /*
  * Save the banner information
  *
  * @param integer $id The banner id to update, null on insert
  * @param array $data The banner information
  * @access public
  * @return boolean
  */
  public static function save($id = null, $data) {
	//echo '<pre>';print_r($data);exit;
  
    global $lC_Database;
    
    $error = false;    
    
    if ( $error === false ) {
      
      if ( is_numeric($id) ) {
        $Qbanner = $lC_Database->query('update :table_shipping_restrict set title = :title, shipping_name = :shipping_name where rule_id = :rule_id');
        $Qbanner->bindInt(':rule_id', $id);
      } else {
        $Qbanner = $lC_Database->query('insert into :table_shipping_restrict (title, shipping_name) values (:title, :shipping_name)');
      }
      
      $Qbanner->bindTable(':table_shipping_restrict', TABLE_SHIPPING_RESTRICT);
      $Qbanner->bindValue(':title', $data['title']);
      $Qbanner->bindValue(':shipping_name', $data['shipping_name']);
      
      $Qbanner->setLogging($_SESSION['module'], $id);
      $Qbanner->execute();

      if ( !$lC_Database->isError() ) {
        return true;
      }
    }

    return false;
  }
 /*
  * Delete the banner record
  *
  * @param integer $id The banner id to delete
  * @param boolean $delete_image True = delete the banner image
  * @access public
  * @return boolean
  */
  public static function delete($id, $title) {
    global $lC_Database;

    $error = false;
	if ( $error === false ) {
    $Qdelete = $lC_Database->query('delete from :table_shipping_restrict where rule_id = :rule_id');
    $Qdelete->bindTable(':table_shipping_restrict', TABLE_SHIPPING_RESTRICT);
    $Qdelete->bindInt(':rule_id', $id);
    $Qdelete->setLogging($_SESSION['module'], $id);
    $Qdelete->execute(); 
	if ( !$lC_Database->isError() ) {
        return true;
      }
	 }
    return false;
  }
 /*
  * Batch delete banner records
  *
  * @param array $batch The banner id's to delete
  * @access public
  * @return boolean
  */
  public static function batchDelete($batch) {
    foreach ( $batch as $id ) {
      lC_Restrict_shipping_Admin::delete($id);
    }
    return true;
  }
}
?>