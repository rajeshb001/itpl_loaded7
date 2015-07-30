<?php
/**
  @package    catalog::admin::applications
  @author     Loaded Commerce
  @copyright  Copyright 2003-2014 Loaded Commerce, LLC
  @copyright  Portions Copyright 2003 osCommerce
  @copyright  Template built on Developr theme by DisplayInline http://themeforest.net/user/displayinline under Extended license 
  @license    https://github.com/loadedcommerce/loaded7/blob/master/LICENSE.txt
  @version    $Id: edit.php v1.0 2013-08-08 datazen $
*/
  global $lC_Vqmod;

  if ( is_numeric($_GET[$lC_Template->getModule()]) ) {
    $pInfo = new lC_ObjectInfo(lC_Products_Admin::get($_GET[$lC_Template->getModule()]));
  }

  if ( is_numeric($_GET[$lC_Template->getModule()]) ) {
    $lC_ObjectInfo = new lC_ObjectInfo(lC_Products_Admin::get($_GET[$lC_Template->getModule()]));
    $attributes = $lC_ObjectInfo->get('attributes');
    $Qpd = $lC_Database->query('select products_name, products_blurb, products_description, products_keyword, products_tags, products_url, language_id from :table_products_description where products_id = :products_id');
    $Qpd->bindTable(':table_products_description', TABLE_PRODUCTS_DESCRIPTION);
    $Qpd->bindInt(':products_id', $lC_ObjectInfo->getInt('products_id'));
    $Qpd->execute();
    $products_name = array();
    $products_blurb = array();
    $products_description = array();
    $products_keyword = array();
    $products_tags = array();
    $products_url = array();
    while ($Qpd->next()) {
      $products_name[$Qpd->valueInt('language_id')] = $Qpd->value('products_name');
      $products_blurb[$Qpd->valueInt('language_id')] = $Qpd->value('products_blurb');
      $products_description[$Qpd->valueInt('language_id')] = $Qpd->value('products_description');
      $products_keyword[$Qpd->valueInt('language_id')] = $Qpd->value('products_keyword');
      $products_tags[$Qpd->valueInt('language_id')] = $Qpd->value('products_tags');
      $products_url[$Qpd->valueInt('language_id')] = $Qpd->value('products_url');
    }
  }
  // get tax class
  $Qtc = $lC_Database->query('select tax_class_id, tax_class_title from :table_tax_class order by tax_class_title');
  $Qtc->bindTable(':table_tax_class', TABLE_TAX_CLASS);
  $Qtc->execute();
  $tax_class_array = array(array('id' => '0',
    'text' => $lC_Language->get('text_none')));
  while ($Qtc->next()) {
    $tax_class_array[] = array('id' => $Qtc->valueInt('tax_class_id'),
      'text' => $Qtc->value('tax_class_title'));
  }
  $tax_options_string = '';
  foreach($tax_class_array as $value) {
    $tax_options_string .= '<option value=\'' . $value['id'] . '\'>' . $value['text'] . '</option>';
  }
  // get shipping rules
  $Qsr = $lC_Database->query('select * from :table_shipping_restrict order by rule_id');
  $Qsr->bindTable(':table_shipping_restrict', TABLE_SHIPPING_RESTRICT);
  $Qsr->execute();
  $shipping_rule_array = array(array('id' => '0',
    'text' => $lC_Language->get('text_none')));
  while ($Qsr->next()) {
    $shipping_rule_array[] = array('id' => $Qsr->valueInt('rule_id'),
      'text' => $Qsr->value('title'));
  }
  $shipping_rule_options_string = '';
  foreach($shipping_rule_array as $shipping_rule) {
    $tax_options_string .= '<option value=\'' . $shipping_rule['id'] . '\'>' . $shipping_rule['text'] . '</option>';
  }
  // get weight class
  $Qwc = $lC_Database->query('select weight_class_id, weight_class_title from :table_weight_class where language_id = :language_id order by weight_class_title');
  $Qwc->bindTable(':table_weight_class', TABLE_WEIGHT_CLASS);
  $Qwc->bindInt(':language_id', $lC_Language->getID());
  $Qwc->execute();
  $weight_class_array = array();
  while ($Qwc->next()) {
    $weight_class_array[] = array('id' => $Qwc->valueInt('weight_class_id'),
      'text' => $Qwc->value('weight_class_title'));
  }
  // get customer groups
  $customer_groups_array = array_merge(array(array('id' => '9999', 'text' => 'All Groups')), lc_get_customer_groups_array());
  $groups_options_string = '';
  foreach($customer_groups_array as $key => $value) {
    $groups_options_string .= '<option value=\'' . $value['id'] . '\'>' . $value['text'] . '</option>';
  }


  // get categories array
  $product_categories_array = array();
  if ( isset($lC_ObjectInfo) ) {
    $Qcategories = $lC_Database->query('select categories_id from :table_products_to_categories where products_id = :products_id');
    $Qcategories->bindTable(':table_products_to_categories', TABLE_PRODUCTS_TO_CATEGORIES);
    $Qcategories->bindInt(':products_id', $lC_ObjectInfo->getInt('products_id'));
    $Qcategories->execute();
    while ($Qcategories->next()) {
      $product_categories_array[] = $Qcategories->valueInt('categories_id');
    }
  }
  $assignedCategoryTree = new lC_CategoryTree();
  $assignedCategoryTree->setBreadcrumbUsage(false);
  $assignedCategoryTree->setSpacerString('&nbsp;', 5); 


  function getCustomerGroupOptionsString($id = null, $esc = false) {
    global $customer_groups_array;
    $options_string = '';
    foreach($customer_groups_array as $value) {
      $options_string_esc .= '<option value=\'' . $value['id'] . '\' ' . (($id == $value['id']) ? 'selected=\'selected\'' : '') . '>' . $value['text'] . '</option>';
      $options_string .= '<option value="' . $value['id'] . '" ' . (($id == $value['id']) ? 'selected="selected"' : '') . '">' . $value['text'] . '</option>';
    }
    if ($esc) {
      return $options_string_esc;
    } else {
      return $options_string;
    }
  }

  function getTaxClassOptionsString($id = null, $esc = false) {
    global $tax_class_array;
    $options_string = '';
    foreach($tax_class_array as $value) {
      $options_string_esc .= '<option value=\'' . $value['id'] . '\' ' . (($id == $value['id']) ? 'selected=\'selected\'' : '') . '>' . $value['text'] . '</option>';
      $options_string .= '<option value="' . $value['id'] . '" ' . (($id == $value['id']) ? 'selected="selected"' : '') . '">' . $value['text'] . '</option>';
    }
    if ($esc) {
      return $options_string_esc;
    } else {
      return $options_string;
    }
  }   
?>
<!-- End main content -->
<script><!--
  var tax_rates = new Array();
  <?php
    foreach ($tax_class_array as $tc_entry) {
      if ( $tc_entry['id'] > 0 ) {
        echo '  tax_rates["' . $tc_entry['id'] . '"] = ' . $lC_Tax->getTaxRate($tc_entry['id']) . ';' . "\n";
      }
    }
  ?>

  function doRound(x, places) {
    return Math.round(x * Math.pow(10, places)) / Math.pow(10, places);
  }

  function getTaxRate(fieldcounter) {
    var selected_value = document.getElementById('tax_class' + fieldcounter).selectedIndex;
    var parameterVal = document.getElementById('tax_class' + fieldcounter).options[selected_value].value;

    if ( (parameterVal > 0) && (tax_rates[parameterVal] > 0) ) {
      return tax_rates[parameterVal];
    } else {
      return 0;
    }
  }

  function updatePrice(field) {
    var modifiedPrice = 0.00;
    var basePrice = Number(<?php echo (isset($lC_ObjectInfo)) ? $lC_ObjectInfo->get('products_price') : 0; ?>);
    var adjValue = Number(document.getElementById('variants_price_adj' + field).value);

    modifiedPrice = doRound(basePrice + adjValue, <?php echo DECIMAL_PLACES; ?>);
    document.getElementById('variants_price_mod' + field).value = modifiedPrice.toFixed(<?php echo DECIMAL_PLACES; ?>);
  }

  function updateGross(field) {
    var fieldcounter = field.substring(14);
    var taxRate = getTaxRate(fieldcounter);
    var grossValue = document.getElementById(field).value;

    // added to update base price field
    document.getElementById('products_base_price').value = grossValue;

    if (taxRate > 0) {
      grossValue = grossValue * ((taxRate / 100) + 1);
    }
    grossValue = doRound(grossValue, <?php echo DECIMAL_PLACES; ?>);
    document.getElementById(field + '_gross').value = grossValue.toFixed(<?php echo DECIMAL_PLACES; ?>);
  }

  function updateNet(field) {
    var fieldcounter = field.substring(14);
    var taxRate = getTaxRate(fieldcounter);
    var netValue = document.getElementById(field + "_gross").value;

    if (taxRate > 0) {
      netValue = netValue / ((taxRate / 100) + 1);
    }
    document.getElementById(field).value = doRound(netValue, <?php echo DECIMAL_PLACES; ?>);
  }
//--></script>
<!-- Edit content -->
<style scoped="scoped">
  .legend { font-weight:bold; font-size: 1.1em; }
  #qq-upload-button2 {
    color: #666666;
    font-size: 12px;
    background-color: transparent;
    text-align: center;
    margin: -15px 0 0 8px;
  }
</style>
<section role="main" id="main">
  <noscript class="message black-gradient simpler"><?php echo $lC_Language->get('ms_error_javascript_not_enabled_warning'); ?></noscript>
  <hgroup id="main-title" class="thin">
    <h1><?php echo (isset($lC_ObjectInfo) && isset($products_name[$lC_Language->getID()])) ? $products_name[$lC_Language->getID()] : $lC_Language->get('heading_title_new_product'); ?></h1>
    <?php
      if ( $lC_MessageStack->exists($lC_Template->getModule()) ) {
        echo $lC_MessageStack->get($lC_Template->getModule());
      }
    ?>
  </hgroup>
  <div id="err-div" class="bold with-padding" style="display:none;color:#ff0000;"><span></span></div>
  <div class="with-padding-no-top small-margin-top">
    <form name="product" id="product" class="dataForm" action="<?php echo lc_href_link_admin(FILENAME_DEFAULT, $lC_Template->getModule() . '=' . (isset($lC_ObjectInfo) ? $lC_ObjectInfo->getInt('products_id') : '') . '&cID=' . $_GET['cID'] . '&action=save'); ?>" method="post" enctype="multipart/form-data">
      <input type="hidden" name="products_id" value="<?php echo (int)$_GET[$lC_Template->getModule()]; ?>">
      <div id="product_tabs" class="side-tabs main-tabs" style="position:relative;">  
        <ul class="tabs">
          <li id="tabHeaderSectionContent" class="active"><?php echo lc_link_object('#section_general_content', $lC_Language->get('section_general')); ?></li>
          <?php if ($pInfo) { ?>
          <li id="tabHeaderSectionImages"><?php echo lc_link_object('#section_images_content', $lC_Language->get('section_images')); ?></li>
          <?php } ?>
          <li id="tabHeaderSectionData"><?php echo lc_link_object('#section_data_content', $lC_Language->get('section_data')); ?></li>
          <li id="tabHeaderSectionOptions"><?php echo lc_link_object('#section_options_content', $lC_Language->get('section_options')); ?></li>
          <li id="tabHeaderSectionPricing"><?php echo lc_link_object('#section_pricing_content', $lC_Language->get('section_pricing')); ?></li>
          <li id="tabHeaderSectionShipping"><?php echo lc_link_object('#section_shipping_content', $lC_Language->get('section_shipping')); ?></li>
          <li id="tabHeaderSectionRelationships"><?php echo lc_link_object('#section_relationships_content', $lC_Language->get('section_relationships')); ?></li>
        </ul>
        <div class="tabs-content">
          <?php 
            include($lC_Vqmod->modCheck('includes/applications/products/pages/tabs/content.php')); 
            include($lC_Vqmod->modCheck('includes/applications/products/pages/tabs/images.php'));  
            include($lC_Vqmod->modCheck('includes/applications/products/pages/tabs/data.php')); 
            include($lC_Vqmod->modCheck('includes/applications/products/pages/tabs/options.php')); 
            include($lC_Vqmod->modCheck('includes/applications/products/pages/tabs/pricing.php'));
            include($lC_Vqmod->modCheck('includes/applications/products/pages/tabs/shipping.php')); 
            include($lC_Vqmod->modCheck('includes/applications/products/pages/tabs/relationships.php')); 
          ?> 
        </div>
      </div>
      <?php echo lc_draw_hidden_field('subaction', 'confirm'); ?>
      <div class="clear-both"></div>
      <div class="six-columns twelve-columns-tablet margin-bottom">
        <div id="buttons-menu-div-listing">
          <div id="buttons-container" style="position: relative;" class="clear-both">
            <div class="align-right">
              <p class="button-height">
                <?php
                  $save = (((int)$_SESSION['admin']['access'][$lC_Template->getModule()] < 2) ? '' : ' onclick="validateForm(\'#product\');"');
                  $close = lc_href_link_admin(FILENAME_DEFAULT, $lC_Template->getModule());
                  echo button_save_close($save, true, $close);
                ?>
                <!-- Save for later functionality reference
                <select class="select expandable-list"> 
                <option id="" value="">Related</option>
                <option id="create_order" value="create_order">Create Order</option>
                <option id="duplicate_product" value="duplicate_product">Duplicate</option>
                <option id="catalog_view" value="catalog_view">View In Catalog</option>
                <option id="view_customers" value="view_customers">View Customers</option>
                <option id="notify_customers" value="notify_customers">Notify Customers</option>
                </select>&nbsp;
                <select class="select expandable-list" style="width:96px;"> 
                <option id="" value="">Actions</option>
                <option id="save" value="save">Save</option>
                <option id="apply_changes" value="apply_changes">Apply</option>
                </select>&nbsp;-->
              </p>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</section>
<?php $lC_Template->loadModal($lC_Template->getModule()); ?>
<!-- End edit content -->
