<form action="<?php echo $action; ?>" accept-charset="utf-8" method="post" id="payment">

</form>
<div class="buttons">
    <div class="pull-right">
	<input type="button" onclick="$('#payment').submit();" value="<?php echo $button_confirm; ?>" id="button-confirm" class="btn btn-primary" data-loading-text="<?php echo $text_loading; ?>" />
	</div>
</div>
