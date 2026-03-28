<?php ob_start(); ?>
<div class="button-header">

	<input type="button" value="Add Property" class="add-link-property" data-function="add-link-property" style="margin-top: 20px;"> 

</div><!-- /.button-header -->
<div data-name="table-container">
%TABLE_CONTENT%
</div>

<div class="button-header" id="showviewbutton" style="margin-top: 20px;">
<input type="button" name="show-file" id="show-file" value="Customer File >>" > 
</div>

<div id="showaddfile" style="margin-top: 20px; display: none;"> 
<div class="button-header">
<input type="button" name="add-file" value="%ADD_FILE%" data-function="add-file"> 
</div>

<div class="form add-file" style="display: none;margin-top: 15px;">
    <form id="form-uploader" name="form_uploader" >
        <div class="input file width-500px">
            <span class="label">File</span>
            <div class="container" >
                <input type="file" name="file[]" multiple class="display-none" id="file-upload">

                <!-- Drag and Drop container-->
                <div class="file-upload-area">
                    <h1>Drag and Drop file here<br/>Or Click "Choose Files"</h1>
                </div>
            </div>
        </div>
        <div id="file-upload-list"></div>
        <div class="input button">
            <span class="input">
                <input type="button" value="Upload" class="upload" style="margin-top: 10px;">
            </span>
        </div>
    </form>
</div>
<div class="files_table_content">
    %FILES_LIST%
</div>
</div>
<script type="text/javascript">

document.getElementById("show-file").onclick = function() {
    this.disabled = true;
}


$( 'tr.file' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {
			if( $( this ).hasClass( 'grayed' ) ) {
				alert( 'You do not have permission to access this resource.' );
			} else {
				customer.file_get( $( this ).data( 'fileid' ));
			}

			e.handled = true;
		}
	} );
  
      
</script>

<?php $content_index = ob_get_clean(); ?>

<?php ob_start(); ?>
<div class="button-header">
	<input type="button" value="Back" class="back" data-function="back-customer">
	<input type="button" value="Save" class="save" data-function="save">
</div><!-- /.button-header -->
<div class="errors"></div>
%FORM_CONTENT%
<?php $content_addedit = ob_get_clean(); ?>
