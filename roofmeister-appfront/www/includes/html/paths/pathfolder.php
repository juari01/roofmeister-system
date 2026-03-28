<?php ob_start(); ?>
<div class="button-header">
	<input type="button" value="Back" class="back-to-path" data-function="back-to-path">
</div><!-- /.button-header -->
<div data-name="table-container">
	%TABLE_CONTENT%
</div>

<script type="text/javascript">

	$(document).ready(function () {

		$(".select-folder-by-id").off().on('click', function (e) {

			e.preventDefault();
			e.stopPropagation();
		
			var path_id = $('[name=path_id]').val();
			var select_folder_id = $(this).data('id');
			var select_folder_name = $(this).prop('name');
			var addlinkpath = "Link " + select_folder_name + " to this path?";

			if (confirm(addlinkpath) == true) {

				load_page('admin/paths', {
					'task': 'index',
					'addlinkfolder': true,
					'folder_id': select_folder_id,
					'path_id': path_id

				}, paths.init_actions);
			}

		});


		$(".back-to-path").off().on('click', function (e) {
			e.preventDefault();
			e.stopPropagation();

			back_page();
		});


	});
</script>

<?php $content_index = ob_get_clean(); ?>



<?php ob_start(); ?>
<div class="button-header">
	<input type="button" value="Back" class="back" data-function="%BACK_BUTTON_FUNCTION%">
	<input type="button" value="Save" class="save" data-function="save">
</div><!-- /.button-header -->
<div class="errors"></div>
%FORM_CONTENT%
<?php $content_addedit = ob_get_clean(); ?>