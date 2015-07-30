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
?>
<style>
#editBanner { padding-bottom:20px; }
#bannerNotes li { margin-right:10px; }  
</style>
<script>
function editBanner(id) {
  var accessLevel = '<?php echo $_SESSION['admin']['access'][$lC_Template->getModule()]; ?>';
  if (parseInt(accessLevel) < 2) {
    $.modal.alert('<?php echo $lC_Language->get('ms_error_no_access');?>');
    return false;
  }
  var jsonLink = '<?php echo lc_href_link_admin('rpc.php', $lC_Template->getModule() . '&action=getFormData&bid=BID'); ?>';  
  $.getJSON(jsonLink.replace('BID', id), 
    function (data) {
      if (data.rpcStatus == -10) { // no session
        var url = "<?php echo lc_href_link_admin(FILENAME_DEFAULT, 'login'); ?>";
        $(location).attr('href',url);
      }
      if (data.rpcStatus != 1) {
        $.modal.alert('<?php echo $lC_Language->get('ms_error_retrieving_data'); ?>');
        return false;
      }  
      $.modal({
          content: '<div id="editBanner">'+
                   '  <div id="editBannerForm">'+
                   '    <form name="bEdit" id="bEdit" action="<?php echo lc_href_link_admin(FILENAME_DEFAULT, $lC_Template->getModule() . '&action=save'); ?>" method="post" enctype="multipart/form-data">'+
                   '      <p><?php echo $lC_Language->get('introduction_edit_shipping_rule'); ?></p>'+
                   '      <p class="button-height inline-label">'+
                   '        <label for="title" class="label"><?php echo $lC_Language->get('field_title'); ?></label>'+
                   '        <?php echo lc_draw_input_field('title', null, 'id="editTitle" class="input" style="width:93%;"'); ?>'+
                   '      </p>'+ 
				   
                   '      <p class="button-height inline-label">'+
                   '        <label for="url" class="label"><?php echo $lC_Language->get('field_shipping_methods'); ?></label>'+
                   '<fieldset class="fieldset"><legend class="legend">United Parcel Service</legend>'+
				   '      <p><input type="checkbox" name="ups[]" value="Next AM">&nbsp;Next AM</p>'+
				   '      <p><input type="checkbox" name="ups[]" value="Next AM Ltr">&nbsp;Next AM Ltr</p>'+
				   '      <p><input type="checkbox" name="ups[]" value="Next Day">&nbsp;Next Day</p>'+
				   '      <p><input type="checkbox" name="ups[]" value="Next Day Ltr">&nbsp;Next Day Ltr</p>'+
				   '      <p><input type="checkbox" name="ups[]" value="Next AM">&nbsp;Next Day PR</p>'+
				   '      <p><input type="checkbox" name="ups[]" value="Next Day Save">&nbsp;Next Day Save</p>'+
				   '      <p><input type="checkbox" name="ups[]" value="Next Day Save Ltr">&nbsp;Next Day Save Ltr</p>'+
				   '      <p><input type="checkbox" name="ups[]" value="2nd Day AM">&nbsp;2nd Day AM</p>'+
				   '      <p><input type="checkbox" name="ups[]" value="2nd Day AM Ltr">&nbsp;2nd Day AM Ltr</p>'+
				   '      <p><input type="checkbox" name="ups[]" value="2nd Day">&nbsp;2nd Day</p>'+
				   '      <p><input type="checkbox" name="ups[]" value="2nd Day Ltr">&nbsp;2nd Day Ltr</p>'+
				   '      <p><input type="checkbox" name="ups[]" value="3 Day Select">&nbsp;3 Day Select</p>'+
				   '      <p><input type="checkbox" name="ups[]" value="Ground">&nbsp;Ground</p>'+
				   '      <p><input type="checkbox" name="ups[]" value="Canada">&nbsp;Canada</p>'+
				   '      <p><input type="checkbox" name="ups[]" value="World Express">&nbsp;World Express</p>'+
				   '      <p><input type="checkbox" name="ups[]" value="World Express Ltr">&nbsp;World Express Ltr</p>'+
				   '      <p><input type="checkbox" name="ups[]" value="World Express Plus">&nbsp;World Express Plus</p>'+
				   '      <p><input type="checkbox" name="ups[]" value="World Express Plus Ltr">&nbsp;World Express Plus Ltr</p>'+
				   '      <p><input type="checkbox" name="ups[]" value="World Expedite">&nbsp;World Expedite</p>'+
				   '</fieldset>'+
                   '      </p>'+
                   '    </form>'+
                   '  </div>'+
                   '</div>',
          title: '<?php echo $lC_Language->get('modal_heading_edit_shipping_rule'); ?>',
          width: 600,
          actions: {
            'Close' : {
              color: 'red',
              click: function(win) { win.closeModal(); }
            }
          },
          buttons: {
            '<?php echo $lC_Language->get('button_close'); ?>': {
              classes:  'glossy',
              click:    function(win) { win.closeModal(); }
            },
            '<?php echo $lC_Language->get('button_save'); ?>': {
              classes:  'blue-gradient glossy',
              click:    function(win) {                
                var bValid = $("#bEdit").validate({
                rules: {
                  title: { required: true }
                  },
                  invalidHandler: function() {
                  }
                }).form();
                if (bValid) {
                  var url = '<?php echo lc_href_link_admin(FILENAME_DEFAULT, $lC_Template->getModule() . '&bid=BID&action=save'); ?>';
                  var actionUrl = url.replace('BID', id);
                  $("#bEdit").attr("action", actionUrl);
                  $("#bEdit").submit();                
                  win.closeModal();
                }
              }
            }
          },
          buttonsLowPadding: true
      });        
      
      $("#editTitle").val(data.bannerData.title);
           
    }              
  ); 
}


</script>