<?php ob_start(); ?>

<div class="button-header">
	<input type="button" value="Back" class="back-note">
	<input type="button" value="Save" class="project-save-note" data-function="project-save-note">
</div>

<div class="errors"></div>
%FORM_CONTENT%
<script type="text/javascript">
	$( document ).ready( function() {
		
	$(".back-note").off().on( 'click', function( e ) {
	e.preventDefault();
	e.stopPropagation();
	back_page();
	} );
	
} );
</script>

<?php $content_addedit = ob_get_clean(); ?>
