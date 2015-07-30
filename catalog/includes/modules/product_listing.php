<?php
/**
  @package    catalog::modules
  @author     Loaded Commerce
  @copyright  Copyright 2003-2014 Loaded Commerce, LLC
  @copyright  Portions Copyright 2003 osCommerce
  @license    https://github.com/loadedcommerce/loaded7/blob/master/LICENSE.txt
  @version    $Id: product_listing.php v1.0 2013-08-08 datazen $
*/
// create column list   
$define_list = array('PRODUCT_LIST_MODEL' => PRODUCT_LIST_MODEL,
                     'PRODUCT_LIST_NAME' => PRODUCT_LIST_NAME,
                     'PRODUCT_LIST_MANUFACTURER' => PRODUCT_LIST_MANUFACTURER,
                     'PRODUCT_LIST_PRICE' => PRODUCT_LIST_PRICE,
                     'PRODUCT_LIST_QUANTITY' => PRODUCT_LIST_QUANTITY,
                     'PRODUCT_LIST_WEIGHT' => PRODUCT_LIST_WEIGHT,
                     'PRODUCT_LIST_IMAGE' => PRODUCT_LIST_IMAGE,
                     'PRODUCT_LIST_BUY_NOW' => PRODUCT_LIST_BUY_NOW);

asort($define_list);

$column_list = array();
reset($define_list);
while (list($key, $value) = each($define_list)) {
  if ($value > 0) $column_list[] = $key;
}

if ($Qlisting->numberOfRows() > 0) {
  
  $output = '';  
  $show_price = true;    
  $show_buy_now = true;    
  while ($Qlisting->next()) {
    // VQMOD-hookpoint; DO NOT MODIFY OR REMOVE THE LINE BELOW
    $lC_Product = new lC_Product($Qlisting->valueInt('products_id'));
    
    if (utility::isB2B() && $lC_Customer->isLoggedOn() === false) {
      $access = (defined('B2B_SETTINGS_GUEST_CATALOG_ACCESS') && B2B_SETTINGS_GUEST_CATALOG_ACCESS > 0) ? (int)B2B_SETTINGS_GUEST_CATALOG_ACCESS : 0;
      if ($access < 66) $show_price = false;
      if ($access < 99) $show_buy_now = false;
    }

    if ( strtotime($lC_Product->getDateAvailable()) <= strtotime(lC_Datetime::getShort()) ) {
      $output .= '<div class="product-listing-module-items">';
      
      for ($col=0, $n=sizeof($column_list); $col<$n; $col++) {
        switch ($column_list[$col]) {
          case 'PRODUCT_LIST_MODEL':
            $output .= '<div class="product-listing-module-model">' . $lC_Product->getModel() . '</div>' . "\n";
            break;

          case 'PRODUCT_LIST_NAME':
            if (isset($_GET['manufacturers'])) {
              $output .= '<div class="product-listing-module-name">' . lc_link_object(lc_href_link(FILENAME_PRODUCTS, $lC_Product->getKeyword() . '&manufacturers=' . $_GET['manufacturers']), $lC_Product->getTitle()) . '</div>' . "\n" .
                        '<div class="product-listing-module-description">' . lc_clean_html($lC_Product->getBlurb()) . '</div>' . "\n";
            } else {
              $output .= '<div class="product-listing-module-name">' . lc_link_object(lc_href_link(FILENAME_PRODUCTS, $lC_Product->getKeyword() . ($cPath ? '&cPath=' . $cPath : '')), $lC_Product->getTitle()) . '</div>' . "\n" . 
                        '<div class="product-listing-module-description">' . lc_clean_html($lC_Product->getBlurb()) . '</div>' . "\n";
            }
            break;

          case 'PRODUCT_LIST_MANUFACTURER':
            if ( $lC_Product->hasManufacturer() ) {
              $output .= '<div class="product-listing-module-manufacturer">' . lc_link_object(lc_href_link(FILENAME_DEFAULT, 'manufacturers=' . $lC_Product->getManufacturerID()), $lC_Product->getManufacturer()) . '</div>' . "\n";
            } else {
              $output .= '<div class="product-listing-module-manufacturer"></div>' . "\n";
            }
            break;

          case 'PRODUCT_LIST_PRICE':
            $output .= '<div class="product-listing-module-price pricing-row">' . (($show_price) ? $lC_Product->getPriceFormated(true) : null) . '</div>' . "\n";
            break;

          case 'PRODUCT_LIST_QUANTITY':
            $output .= '<div class="product-listing-module-quantity">' . $lC_Product->getQuantity() . '</div>' . "\n";
            break;

          case 'PRODUCT_LIST_WEIGHT':
            $output .= '<div class="product-listing-module-weight">' . $lC_Product->getWeight() . '</div>' . "\n";
            break; 

          case 'PRODUCT_LIST_IMAGE':
            if (isset($_GET['manufacturers'])) {
              $output .= '<div class="product-listing-module-image">' . lc_link_object(lc_href_link(FILENAME_PRODUCTS, $lC_Product->getKeyword() . '&manufacturers=' . $_GET['manufacturers']), $lC_Image->show($lC_Product->getImage(), $lC_Product->getTitle(), 'class="product-listing-module-image-src"')) . '</div>' . "\n";
            } else {
              $output .= '<div class="product-listing-module-image">' . lc_link_object(lc_href_link(FILENAME_PRODUCTS, $lC_Product->getKeyword() . ($cPath ? '&cPath=' . $cPath : '')), $lC_Image->show($lC_Product->getImage(), $lC_Product->getTitle(), 'class="product-listing-module-image-src"')) . '</div>' . "\n";
            }
            break;
            
          case 'PRODUCT_LIST_BUY_NOW':
            if (DISABLE_ADD_TO_CART == 1 && $lC_Product->getQuantity() < 1) {
              $output .= '<div class="product-listing-module-buy-now buy-btn-div"><button class="product-listing-module-buy-now-button" disabled>' . $lC_Language->get('out_of_stock') . '</button></div>' . "\n"; 
            } else {
              $output .= '<div class="product-listing-module-buy-now buy-btn-div">';
              if ($show_buy_now) $output .= '<form action="' . lc_href_link(basename($_SERVER['SCRIPT_FILENAME']), $lC_Product->getKeyword() . '&' . lc_get_all_get_params(array('action', 'new')) . '&action=cart_add') . '" method="post"><button onclick="$(this).closest(\'form\').submit();" type="submit" class="product-listing-module-buy-now-button">' . $lC_Language->get('button_buy_now') . '</button></form>';
              $output .= '</div>' . "\n"; 
            }
            break;
        }
      }
      $output .= '</div>' . "\n";
    }
  }     
} else {
  $output .= '<div class="product-listing-module-no-products"><p>' . $lC_Language->get('no_products_in_category') . '</p></div>';
}

echo $output;
?>