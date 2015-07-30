<?php
/**  
  @package    catalog::templates::content
  @author     Loaded Commerce
  @copyright  Copyright 2003-2014 Loaded Commerce, LLC
  @copyright  Portions Copyright 2003 osCommerce
  @copyright  Template built on DevKit http://www.bootstraptor.com under GPL license 
  @license    https://github.com/loadedcommerce/loaded7/blob/master/LICENSE.txt
  @version    $Id: checkout_shipping.php v1.0 2013-08-08 datazen $
*/ 
?>
<!--content/checkout/checkout_shipping.php start-->
<div class="row">
  <div class="col-sm-12 col-lg-12 large-margin-bottom">  
    <h1 class="no-margin-top"><?php echo $lC_Language->get('text_checkout'); ?></h1>
    <?php 
    if(isset($_SESSION['coupon_msg']) && $_SESSION['coupon_msg'] != '') {
      $lC_MessageStack->add('shopping_cart', $_SESSION['coupon_msg'], 'success');
      unset($_SESSION['coupon_msg']);
      if ( $lC_MessageStack->size('shopping_cart') > 0 ) echo '<div class="message-stack-container alert alert-success small-margin-bottom">' . $lC_MessageStack->get('shopping_cart') . '</div>' . "\n"; 
    }
    if(isset($_SESSION['remove_coupon_msg']) && $_SESSION['remove_coupon_msg'] != '') {
      $lC_MessageStack->add('shopping_cart', $_SESSION['remove_coupon_msg'], 'warning');
      unset($_SESSION['remove_coupon_msg']);
      if ( $lC_MessageStack->size('shopping_cart') > 0 ) echo '<div class="message-stack-container alert alert-warning small-margin-bottom">' . $lC_MessageStack->get('shopping_cart') . '</div>' . "\n"; 
    }  
    ?>
    <form name="checkout_shipping" id="checkout_shipping" action="<?php echo lc_href_link(FILENAME_CHECKOUT, 'shipping=process', 'SSL'); ?>" method="post">
      <div id="content-checkout-shipping-container">      
        <div class="panel panel-default no-margin-bottom">
          <div class="panel-heading">
            <h3 class="no-margin-top no-margin-bottom"><?php echo $lC_Language->get('box_ordering_steps_delivery'); ?></h3>
          </div>
          <div class="panel-body no-padding-bottom">
            <div class="row">
              <div class="col-sm-4 col-lg-4">
                <div class="well relative no-padding-bottom">  
                  <h4 class="no-margin-top"><?php echo $lC_Language->get('ship_to_address'); ?></h4>
                  <address>
                    <?php echo lC_Address::format($lC_ShoppingCart->getShippingAddress(), '<br />'); ?>                
                  </address>
                  <div class="checkbox">
                    <input type="checkbox" name="shipto_as_billable" id="shipto_as_billable"><label class="small-margin-left"><?php echo $lC_Language->get('billable_address_checkbox'); ?></label>
                  </div>
                  <div class="btn-group clearfix absolute-top-right small-padding-right small-padding-top">
                    <button type="button" onclick="document.location.href='<?php echo lc_href_link(FILENAME_CHECKOUT, 'shipping_address', 'SSL'); ?>'" class="btn btn-default btn-xs"><?php echo $lC_Language->get('button_edit'); ?></button>
                  </div>
                </div>
                <div class="well">
                  <?php 
                  $total = 0;
                  foreach ($lC_ShoppingCart->getOrderTotals() as $module) { 
                    if ($module['code'] == 'terms_handling') continue;
                    $title = (strstr($module['title'], '(')) ? substr($module['title'], 0, strpos($module['title'], '(')) . ':' : $module['title'];
                    $class = str_replace(':', '', $title);
                    $class = 'ot-' . strtolower(str_replace(' ', '-', $class));
                    if ($module['code'] != 'total') $total = $total + (float)$module['value'];
                    if ($module['code'] == 'total') {
                      $module['value'] = $total;
                      $module['text'] = '<b>' . $lC_Currencies->displayPrice($total,1) . '</b>';
                    }
                    ?>
                    <div class="clearfix">
                      <?php 
                      echo '<div class="clearfix">' .
                           '  <span class="pull-left ' . $class . ' ot-' . $module['code'] . '-title"">' . $title . '</span>' .
                           '  <span class="pull-right ' . $class . ' ot-' . $module['code'] . '-text">' . $module['text'] . '</span>' .'</div>';  
                      ?>  
                    </div>                    
                    <?php
                  }
                  ?>                
                </div>       
              </div>
              <div class="col-sm-8 col-lg-8">
                <div class="">
                  <?php
				  /*start of shippng rule */
				  foreach ($lC_ShoppingCart->getProducts() as $products) {
				  $product_id = $products['id'];
					//echo  $lC_ShoppingCart->shippingrule($product_id);
				  }
				  $shippng_rule_id = $lC_ShoppingCart->shippingruleid($product_id);
				  $shippng_rule_seriarray = $lC_ShoppingCart->shippingrule($product_id);
				  $shippng_rule_array = unserialize($shippng_rule_seriarray);
				  //echo '<pre>';print_r($shippng_rule_array);
				  /*end of shipping rule*/
                  if ($lC_Shipping->hasQuotes()) { 
                    ?>
                    <h3><?php echo $lC_Language->get('shipping_method_title'); ?></h3>
                    <?php 
                    echo ($lC_Shipping->numberOfQuotes() > 1) ? '<div class="alert alert-warning">' . $lC_Language->get('choose_shipping_method') . '</div>' : '<div class="alert alert-warning">' . $lC_Language->get('only_one_shipping_method_available') . '</div>' . "\n"; 

                    $radio_buttons = 0;
                    foreach ($lC_Shipping->getQuotes() as $quotes) {
                      ?>
                      <h4><?php echo $quotes['module']; ?></h4>
                      <table class="content-shipping-methods-table table table-hover table-responsive">
                        <?php
                        if (isset($quotes['error'])) {
                          ?>
                          <tr><td colspan="3" class=""><?php echo $quotes['error']; ?></td></tr>
                          <?php
                        } else {
                          $counter = 0;                   
                          foreach ($quotes['methods'] as $methods) {
						  //echo '<pre>';print_r($methods);
						  if($shippng_rule_id > 0){
						  if (in_array($methods['title'], $shippng_rule_array))
						  {						  
						  
                            if (($quotes['id'] . '_' . $methods['id'] == $lC_ShoppingCart->getShippingMethod('id')) || $lC_Shipping->numberOfQuotes() == 1) {
                              echo '<tr class="module-row-selected cursor-pointer" id="default-selected" onclick="selectRowEffect(this, ' . $radio_buttons . ')">' . "\n";
                            } else {
                              echo '<tr class="module-row cursor-pointer" onclick="selectRowEffect(this, ' . $radio_buttons . ')">' . "\n";
                            }
                            ?>
                            <td><?php echo $methods['title']; ?></td>
                            <?php
                            if ( ($lC_Shipping->numberOfQuotes() > 1) || (sizeof($quotes['methods']) > 1) ) {
                              ?>
                              <td><?php echo $lC_Currencies->displayPrice($methods['cost'], $quotes['tax_class_id']); ?></td>
                              <td class="text-right"><?php echo lc_draw_radio_field('shipping_mod_sel', $quotes['id'] . '_' . $methods['id'], $lC_ShoppingCart->getShippingMethod('id'), 'id="' . $quotes['id'] . '_' . $counter . '"',''); ?></td>
                              <?php
                            } else {
                              ?>
                              <td class="content-checkout-listing-blank"></td>
                              <td class="text-right"><?php echo $lC_Currencies->displayPrice($methods['cost'], $quotes['tax_class_id']) . lc_draw_hidden_field('shipping_mod_sel', $quotes['id'] . '_' . $methods['id']); ?></td>
                              <?php
                            }
                            ?>
                            </tr>
                            <?php
                            $counter++;
                            $radio_buttons++;
                          }
						  }
						  else{
						  if (($quotes['id'] . '_' . $methods['id'] == $lC_ShoppingCart->getShippingMethod('id')) || $lC_Shipping->numberOfQuotes() == 1) {
                              echo '<tr class="module-row-selected cursor-pointer" id="default-selected" onclick="selectRowEffect(this, ' . $radio_buttons . ')">' . "\n";
                            } else {
                              echo '<tr class="module-row cursor-pointer" onclick="selectRowEffect(this, ' . $radio_buttons . ')">' . "\n";
                            }
                            ?>
                            <td><?php echo $methods['title']; ?></td>
                            <?php
                            if ( ($lC_Shipping->numberOfQuotes() > 1) || (sizeof($quotes['methods']) > 1) ) {
                              ?>
                              <td><?php echo $lC_Currencies->displayPrice($methods['cost'], $quotes['tax_class_id']); ?></td>
                              <td class="text-right"><?php echo lc_draw_radio_field('shipping_mod_sel', $quotes['id'] . '_' . $methods['id'], $lC_ShoppingCart->getShippingMethod('id'), 'id="' . $quotes['id'] . '_' . $counter . '"',''); ?></td>
                              <?php
                            } else {
                              ?>
                              <td class="content-checkout-listing-blank"></td>
                              <td class="text-right"><?php echo $lC_Currencies->displayPrice($methods['cost'], $quotes['tax_class_id']) . lc_draw_hidden_field('shipping_mod_sel', $quotes['id'] . '_' . $methods['id']); ?></td>
                              <?php
                            }
                            ?>
                            </tr>
                            <?php
                            $counter++;
                            $radio_buttons++;
						  }
						  }
                        }
                        ?>
                      </table>
                      <?php                          
                    }
                  }
                  ?>                
                </div> 
                <div class="btn-set clearfix">
                  <button class="btn btn-lg btn-success pull-right" onclick="$('#checkout_shipping').submit();" type="button"><?php echo $lC_Language->get('button_continue'); ?></button>
                  <button class="btn btn-lg btn-default" onclick="window.location.href='<?php echo lc_href_link(FILENAME_CHECKOUT, '', 'SSL'); ?>'" type="button"><?php echo $lC_Language->get('button_back'); ?></button>
                </div> 
                <?php
                if ($lC_Customer->isLoggedOn() !== false) {
                  if (defined('MODULE_SERVICES_INSTALLED') && in_array('coupons', explode(';', MODULE_SERVICES_INSTALLED)) && 
                      defined('SERVICE_COUPONS_DISPLAY_ON_CART_PAGE') && SERVICE_COUPONS_DISPLAY_ON_SHIPPING_PAGE == '1') {
                    ?>
                    <div class="well">
                      <h3 class="no-margin-top"><?php echo $lC_Language->get('text_coupon_code_heading'); ?></h3>
                      <p><?php echo $lC_Language->get('text_coupon_code_instructions'); ?></p>
                      <div class="form-group">
                        <label class="sr-only"></label><input type="text" name="coupon_code" id="coupon_code" class="form-control">
                      </div>
                      <div class="btn-set clearfix no-margin-top no-margin-bottom">
                        <button type="button" class="btn btn-primary pull-right" onclick="addCoupon();"><?php echo $lC_Language->get('text_apply_coupon'); ?></button>
                      </div>
                    </div>
                    <?php 
                  } 
                }
                ?>
              </div>
            </div>        
          </div>
        </div>  
        <div class="clearfix panel panel-default no-margin-bottom">
          <div class="panel-heading">
            <h3 class="no-margin-top no-margin-bottom"><?php echo $lC_Language->get('box_ordering_steps_payment'); ?></h3>
          </div>
        </div>     
        <div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="no-margin-top no-margin-bottom"><?php echo $lC_Language->get('box_ordering_steps_confirmation'); ?></h3>
          </div>
        </div> 
      </div> 
    </form> 
  </div>
</div> 
<!--content/checkout/checkout_shipping.php end-->