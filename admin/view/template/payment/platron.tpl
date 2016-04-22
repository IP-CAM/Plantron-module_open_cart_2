<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button onclick="$('#form').submit();" type="submit" form="form-cod" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  
<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<div class="box">
  <div class="left"></div>
  <div class="right"></div>
  <div class="content">
    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
	<div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $entry_payment_name; ?>*</label>
            <div class="col-sm-10">
              <input type="text" name="platron_payment_name" value="<?php echo $platron_payment_name; ?>" class="form-control" />
            </div>
			 <br />
                  <?php if ($error_payment_name) { ?>
                  <span class="error"><?php echo $error_payment_name; ?></span>
                  <?php } ?>				  
     </div>
	 <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $entry_merchant_id; ?>*</label>
            <div class="col-sm-10">
              <input type="text" name="platron_merchant_id" value="<?php echo $platron_merchant_id; ?>" class="form-control" />
            </div>
			<br />
			  <?php if ($error_merchant_id) { ?>
			  <span class="error"><?php echo $error_merchant_id; ?></span>
			  <?php } ?>				  
     </div>
	 <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $entry_secret_word; ?>*</label>
            <div class="col-sm-10">
              <input type="text" name="platron_secret_word" value="<?php echo $platron_secret_word; ?>" class="form-control" />
            </div>
			<br />
			  <?php if ($error_merchant_id) { ?>
			  <span class="error"><?php echo $error_merchant_id; ?></span>
			  <?php } ?>				  
     </div>
	 <div class="form-group">
            <label class="col-sm-2 control-label">Result URL:</label>
            <div class="col-sm-10">
              <?php echo $copy_result_url; ?>
            </div>			  
     </div>
	 <div class="form-group">
            <label class="col-sm-2 control-label">Success URL:</label>
            <div class="col-sm-10">
              <?php echo $copy_success_url; ?>
            </div>			  
     </div>
	 <div class="form-group">
            <label class="col-sm-2 control-label">Fail URL:</label>
            <div class="col-sm-10">
              <?php echo $copy_fail_url; ?>
            </div>			  
     </div>
	 <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $entry_test; ?></label>
            <div class="col-sm-10">
              <select name="platron_test" id="input-order-status" class="form-control">
                      <?php if($platron_test == 1) { ?>
                      <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                      <option value="0"><?php echo $text_disabled; ?></option>
                      <?php } else { ?>
                      <option value="1"><?php echo $text_enabled; ?></option>
                      <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                      <?php } ?>
                  </select>
            </div>				  
     </div>
	 <div class="form-group"></div>
	 <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $entry_lifetime; ?></label>
            <div class="col-sm-10">
              <input type="text" name="platron_lifetime" value="<?php echo $platron_lifetime; ?>" class="form-control" />
            </div>			  
     </div>
<div class="form-group"></div>
	 <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $entry_order_status; ?></label>
            <div class="col-sm-10">
              <select name="platron_order_status_id" id="input-order-status" class="form-control">
				   <?php foreach ($order_statuses as $order_status) { ?>
					<?php if ($order_status['order_status_id'] == $platron_order_status_id) { ?>
					<option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
					<?php } else { ?>
					<option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
					<?php } ?>
					<?php } ?>
				  </select>
            </div>				  
     </div>
	 <div class="form-group"></div>
	 <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $entry_order_status_fail; ?></label>
            <div class="col-sm-10">
              <select name="platron_order_status_id_fail" id="input-order-status_fail" class="form-control">
				   <?php foreach ($order_statuses_fail as $order_status) { ?>
					<?php if ($order_status['order_status_id'] == $platron_order_status_id_fail) { ?>
					<option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
					<?php } else { ?>
					<option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
					<?php } ?>
					<?php } ?>
				  </select>
            </div>				  
     </div>
	 <div class="form-group"></div>
	 <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $entry_status; ?></label>
            <div class="col-sm-10">
              <select name="platron_status" id="input-order-status" class="form-control">
				   <?php if ($platron_status) { ?>
					<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
					<option value="0"><?php echo $text_disabled; ?></option>
					<?php } else { ?>
					<option value="1"><?php echo $text_enabled; ?></option>
					<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
					<?php } ?>
				  </select>
            </div>				  
     </div>
	  <div class="form-group"></div>
	  <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $entry_sort_order; ?></label>
            <div class="col-sm-10">
              <input type="text" name="platron_sort_order" value="<?php echo $platron_sort_order; ?>"  class="form-control" />			  
     </div>
    </form>
  </div>
</div>
<?php echo $footer; ?>