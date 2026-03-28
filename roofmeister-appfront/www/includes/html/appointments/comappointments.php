<?php ob_start(); ?>
<div class="row">
      <div class="col-1">
	    <input type="button" value="Add Appointment" data-function="add-appointment"> 
		<span class="page_nav" data-name="page-container">%PAGES%</span>
      </div>
      <div class="col-2">
	  <span class="search"><input type="text" id="search" name="search" class="search-appointment" placeholder=" search..."></span>
      </div>
</div>

<div class="table-container" data-name="table-container">
%TABLE_CONTENT%
</div>
<?php $content_index = ob_get_clean(); ?>

<?php ob_start(); ?>
<div class="button-header">
	<input type="button" value="Back" class="back" data-function="%BACK_BUTTON_FUNCTION%">
	<input type="button" value="Save" class="save" data-function="save" %DISABLED%>
</div><!-- /.button-header -->
<div class="errors"></div>
%FORM_CONTENT%
<?php $content_addedit = ob_get_clean(); ?>
