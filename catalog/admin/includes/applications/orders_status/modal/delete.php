<?php
/**
  @package    catalog::admin::applications
  @author     Loaded Commerce
  @copyright  Copyright 2003-2014 Loaded Commerce, LLC
  @copyright  Portions Copyright 2003 osCommerce
  @copyright  Template built on Developr theme by DisplayInline http://themeforest.net/user/displayinline under Extended license 
  @license    https://github.com/loadedcommerce/loaded7/blob/master/LICENSE.txt
  @version    $Id: delete.php v1.0 2013-08-08 datazen $
*/
?>
<script>
function deleteStatus(id, name) {
  var accessLevel = '<?php echo $_SESSION['admin']['access'][$lC_Template->getModule()]; ?>';
  if (parseInt(accessLevel) < 4) {
    $.modal.alert('<?php echo $lC_Language->get('ms_error_no_access');?>');
    return false;
  }
  var jsonLink = '<?php echo lc_href_link_admin('rpc.php', $lC_Template->getModule() . '&action=getFormData&osid=OSID'); ?>';
  $.getJSON(jsonLink.replace('OSID', parseInt(id)),
    function (data) {
      if (data.rpcStatus == -10) { // no session
        var url = "<?php echo lc_href_link_admin(FILENAME_DEFAULT, 'login'); ?>";
        $(location).attr('href',url);
      }
      if (data.rpcStatus != 1) {
        if (data.rpcStatus == -2) {
          $.modal.alert('<?php echo $lC_Language->get('delete_error_order_status_in_use'); ?> ' + data.totalOrders + ' <?php echo $lC_Language->get('delete_error_order_status_in_use_end'); ?>');
        } else if (data.rpcStatus == -3) {
          $.modal.alert('<?php echo $lC_Language->get('delete_error_order_status_used'); ?> ' + data.totalOrders + ' <?php echo $lC_Language->get('delete_error_order_status_used_end'); ?>');
        } else {
          $.modal.alert('<?php echo $lC_Language->get('ms_error_retrieving_data'); ?>');
        }
        return false;
      }
      $.modal({
        content: '<div id="deleteStatus">'+
                 '  <div id="deleteConfirm">'+
                 '    <p id="deleteConfirmMessage"><?php echo $lC_Language->get('introduction_delete_order_status'); ?>'+
                 '      <p><b>' + decodeURI(name.replace(/\+/g, '%20')) + '</b></p>'+
                 '    </p>'+
                 '  </div>'+
                 '</div>',
        title: '<?php echo $lC_Language->get('modal_heading_delete_order_status'); ?>',
        width: 300,
            actions: {
          'Close' : {
            color: 'red',
            click: function(win) { win.closeModal(); }
          }
        },
        buttons: {
          '<?php echo $lC_Language->get('button_cancel'); ?>': {
            classes:  'glossy',
            click:    function(win) { win.closeModal(); }
          },
          '<?php echo $lC_Language->get('button_delete'); ?>': {
            classes:  'blue-gradient glossy',
            click:    function(win) {
              var jsonLink = '<?php echo lc_href_link_admin('rpc.php', $lC_Template->getModule() . '&action=deleteStatus&osid=OSID'); ?>';
              $.getJSON(jsonLink.replace('OSID', parseInt(id)),
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
              win.closeModal();
            }
          }
        },
        buttonsLowPadding: true
      });
    }
  );
}
</script>