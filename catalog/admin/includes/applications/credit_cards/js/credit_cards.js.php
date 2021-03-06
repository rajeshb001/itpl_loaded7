<?php
/**
  @package    catalog::admin::applications
  @author     Loaded Commerce
  @copyright  Copyright 2003-2014 Loaded Commerce, LLC
  @copyright  Portions Copyright 2003 osCommerce
  @copyright  Template built on Developr theme by DisplayInline http://themeforest.net/user/displayinline under Extended license 
  @license    https://github.com/loadedcommerce/loaded7/blob/master/LICENSE.txt
  @version    $Id: credit_cards.js.php v1.0 2013-08-08 datazen $
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
    "bJQueryUI": false,
    "sDom": 'T<"clear"><"H"fl>rt<"F"ip<"clear">',
    "sPaginationType": paginationType,
    "aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
    "aoColumns": [{ "sWidth": "60%", "bSortable": true, "sClass": "dataColCard" },
                  { "sWidth": "10%", "bSortable": false, "sClass": "dataColActive" },
                  { "sWidth": "10%", "bSortable": true, "sClass": "hide-on-mobile-portrait dataColSort" },
                  { "sWidth": "20%", "bSortable": false, "sClass": "dataColAction" }]
  });
  $('#dataTable').responsiveTable();
       
  if ($.template.mediaQuery.isSmallerThan('tablet-portrait')) {
    $('#main-title > h1').attr('style', 'font-size:1.8em;');
    $('#main-title').attr('style', 'padding: 0 0 0 20px;');
    $('#dataTable_info').attr('style', 'position: absolute; bottom: 42px; color:#4c4c4c;');
    $('#dataTable_length').hide();
    $('#actionText').hide();
    $('.on-mobile').show();
  }  
});

function updateStatus(id, cstatus) {
  var jsonLink = '<?php echo lc_href_link_admin('rpc.php', $lC_Template->getModule() . '&action=updateStatus&ccid=CCID&cstatus=CSTATUS'); ?>'
  $.getJSON(jsonLink.replace('CCID', parseInt(id)).replace('CSTATUS', cstatus),
    function (data) {
      if (data.rpcStatus == -10) { // no session
        var url = "<?php echo lc_href_link_admin(FILENAME_DEFAULT, 'login'); ?>";
        $(location).attr('href',url);
      }
      if (data.rpcStatus != 1) {
        $.modal.alert('<?php echo $lC_Language->get('ms_error_action_not_performed'); ?>');
        return false;
      }
      oTable.fnReloadAjax();
    }
  );
}
</script>