<?php
/**
  @package    catalog::admin::applications
  @author     Loaded Commerce
  @copyright  Copyright 2003-2014 Loaded Commerce, LLC
  @copyright  Portions Copyright 2003 osCommerce
  @copyright  Template built on Developr theme by DisplayInline http://themeforest.net/user/displayinline under Extended license 
  @license    https://github.com/loadedcommerce/loaded7/blob/master/LICENSE.txt
  @version    $Id: file_manager.js.php v1.0 2013-08-08 datazen $
*/
global $lC_Template, $lC_Language;
?>
<script>
$(document).ready(function() {
  var paginationType = ($.template.mediaQuery.isSmallerThan('tablet-portrait')) ? 'two_button' : 'full_numbers';            
  var dataTableDataURL = '<?php echo lc_href_link_admin('rpc.php', $lC_Template->getModule() . '&action=getAll&media=MEDIA'); ?>';   
  oTable = $('#dataTable').dataTable({
    "bProcessing": true,
    "sAjaxSource": dataTableDataURL.replace('MEDIA', $.template.mediaQuery.name),
    "sPaginationType": paginationType,
    "bPaginate": false,
    "bSort": false, 
    "aoColumns": [{ "sWidth": "20%", "sClass": "dataColFiles" },
                  { "sWidth": "10%", "sClass": "dataColSize hide-on-mobile-portrait" },
                  { "sWidth": "10%", "sClass": "dataColPerms hide-on-mobile-portrait" },
                  { "sWidth": "10%", "sClass": "dataColUser hide-on-tablet" },
                  { "sWidth": "10%", "sClass": "dataColGroup hide-on-tablet" },
                  { "sWidth": "5%", "sClass": "dataColWrite hide-on-mobile-portrait" },
                  { "sWidth": "15%", "sClass": "dataColLast hide-on-tablet" },
                  { "sWidth": "20%", "sClass": "dataColAction" }]
  });
  $('#dataTable').responsiveTable();
       
  if ($.template.mediaQuery.isSmallerThan('tablet-portrait')) {
    $('#main-title > h1').attr('style', 'font-size:1.8em;');
    $('#main-title').attr('style', 'padding: 0 0 0 20px;');
    $('#dataTable_info').attr('style', 'position: absolute; bottom: 42px; color:#4c4c4c;');
    $('#dataTable_length').hide();
    $('#actionText').hide();
    $('.on-mobile').show();
    $('.selectContainer').hide();
  }
  
  var error = '<?php echo $_SESSION['error']; ?>';
  if (error) {
    var errmsg = '<?php echo $_SESSION['errmsg']; ?>';
    $.modal.alert(errmsg);
  }
  
  // breadcrumb last li css
  $(".fm-breadcrumb li:last-child").addClass("last");
});
</script>