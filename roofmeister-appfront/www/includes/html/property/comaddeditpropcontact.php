<?php ob_start(); ?>
<div class="button-header">
	<input type="button" value="Back" class="back" data-function="back_propcontact">
	<input type="button" value="Save" class="save" data-function="savepropcontact">
</div><!-- /.button-header -->
<div class="errors"></div>
%FORM_CONTENT%
<?php $content_addedit = ob_get_clean(); ?>
