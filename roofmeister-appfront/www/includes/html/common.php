<?php ob_start(); ?>
<div class="button-header">
	<input type="button" value="%ADD_BUTTON_VALUE%" class="%CLASSADD%" data-function="%ADD_BUTTON_FUNCTION%" style="display: %ADDDISPLAY%;"> 
	
</div><!-- /.button-header -->
<div data-name="table-container">
%TABLE_CONTENT%
</div>
<?php $content_index = ob_get_clean(); ?>

<?php ob_start(); ?>
<div class="button-header">
	<input type="button" value="Back" class="back" data-function="%BACK_BUTTON_FUNCTION%">
	<input type="button" value="Save" class="save" data-function="save">
</div><!-- /.button-header -->
<div class="errors"></div>
%FORM_CONTENT%
<?php $content_addedit = ob_get_clean(); ?>
