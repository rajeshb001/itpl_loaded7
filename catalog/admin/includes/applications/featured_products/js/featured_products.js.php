<?php
/**
  @package    catalog::admin::applications
  @author     Loaded Commerce
  @copyright  Copyright 2003-2014 Loaded Commerce, LLC
  @copyright  Portions Copyright 2003 osCommerce
  @copyright  Template built on Developr theme by DisplayInline http://themeforest.net/user/displayinline under Extended license 
  @license    https://github.com/loadedcommerce/loaded7/blob/master/LICENSE.txt
  @version    $Id: featured_products.js.php v1.0 2013-08-08 datazen $
*/
global $lC_Template, $lC_Language;
?>
<script>
$(document).ready(function() {
  var paginationType = ($.template.mediaQuery.isSmallerThan('tablet-portrait')) ? 'two_button' : 'full_numbers';            
  var dataTableDataURL = '<?php echo lc_href_link_admin('rpc.php', $lC_Template->getModule() . '&action=getAll&media=MEDIA'); ?>'; 
  var quickAdd = '<?php echo (isset($_GET['action']) && $_GET['action'] == 'quick_add') ? true : false; ?>';
    
  oTable = $('#dataTable').dataTable({
    "bProcessing": true,
    "sAjaxSource": dataTableDataURL.replace('MEDIA', $.template.mediaQuery.name),
    "sPaginationType": paginationType, 
    "aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
    "iDisplayLength": 25,
    "aoColumns": [{ "sWidth": "10px", "bSortable": false, "sClass": "dataColCheck hide-on-mobile" },
                  { "sWidth": "50%", "bSortable": true, "sClass": "dataColName" },
                  { "sWidth": "15%", "bSortable": true, "sClass": "dataColExpires hide-on-mobile", "sType": "string" },
                  { "sWidth": "10%", "bSortable": true, "sClass": "dataColStatus hide-on-mobile", "sType": "string" },
                  { "sWidth": "25%", "bSortable": false, "sClass": "dataColAction" }]
  });
  $('#dataTable').responsiveTable();
  
  if ($.template.mediaQuery.isSmallerThan('tablet-portrait')) {
    $('#dataTable_info').attr('style', 'position: absolute; bottom: 42px; color:#4c4c4c;');
    $('#dataTable_length').hide();
    $('#actionText').hide();
    $('.on-mobile').show();
    $('.selectContainer').hide();
  }
  
  $('.datepicker').glDatePicker({ zIndex: 100 });
  
});

function updateStatus(id, val) {
  var jsonLink = '<?php echo lc_href_link_admin('rpc.php', $lC_Template->getModule() . '&action=updateStatus&fid=FID&val=VAL'); ?>';
  $.getJSON(jsonLink.replace('FID', id).replace('VAL', val));
  if (val == 1) {               
    $("#status_" + id).attr('onclick', 'updateStatus(\'' + id + '\', \'-1\')');
    $("#status_" + id).html('<span class="icon-tick icon-size2 icon-green cursor-pointer with-tooltip" title="<?php echo $lC_Language->get('text_disable'); ?>"></span>');
  } else {               
    $("#status_" + id).attr('onclick', 'updateStatus(\'' + id + '\', \'1\')');
    $("#status_" + id).html('<span class="icon-cross icon-size2 icon-red cursor-pointer with-tooltip" title="<?php echo $lC_Language->get('text_enable'); ?>"></span>');
  }
}

function validateForm(e) {
  // turn off messages
  jQuery.validator.messages.required = "";

  var bValid = $("#featured").validate({
    invalidHandler: function() {
    },
    rules: {
      'products_id': {
        required: true,
      },
    },    
    messages: {
      "products_id": "<?php echo $lC_Language->get('ms_error_featured_products_products_id_required'); ?>",
    } 
  }).form();
  if (bValid) {
    $(e).submit();
  } 

  return false;
}
</script>