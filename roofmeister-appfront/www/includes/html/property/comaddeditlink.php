<?php ob_start(); ?>
<div class="button-header">

	<input type="button" value="Add Customer" class="add-link-customer" data-function="add-link-customer" style="margin-top: 20px;"> 

</div><!-- /.button-header -->
<div data-name="table-container">
%TABLE_CONTENT%
</div>


<div class="button-header" id="showviewbutton" style="margin-top: 20px;">
<input type="button" name="show-file" id="show-file" value="Property File >>" > 
</div>

<div id="showaddfile" style="margin-top: 20px; display: block; display: none;"> 
<div class="button-header">
<input type="button" name="add-file" value="%ADD_FILE%" data-function="add-file">
<input type="button" name="add-folder" value="%ADD_FOLDER%" data-function="add-folder"> 
</div>

<div class="form add-file" style="display: none;margin-top: 15px;">
    <form id="form-uploader" name="form_uploader" >
        <div class="input file width-500px">
            <span class="label">File</span>
            <div class="container" >
                <input type="file" name="file[]" multiple class="display-none" id="file-upload" accept="image/*">

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

<div class="form add-folder" style="display: none; margin-top: 15px;">
	<form>
          <div class="input text">
            <span class="label">Folder Name</span><br>
            <span class="input">
                <input type="text" name="folder_name">
            </span>
        </div>
		<div class="input button">
            <span class="input">
                <input type="button" value="Create Folder" class="create-folder">
            </span>
        </div> 
	</form>
</div><!-- /.add-folder --> 

<br>
<div id="folder_breadcrumbs">
<a class="breadcrumbsclick" href="#">
%BREADCRUMBS_LIST% 
</a>
</div>

<div class="files_table_content">
    %FILES_LIST%
</div>
</div>

<script type="text/javascript">

document.getElementById("show-file").onclick = function() {
    this.disabled = true;
}

var folder_id = $('[name=folder_id]').val();
var property_id = $('[name=property_id]').val();


console.log(folder_id);

if ( !property_id ) {
	showviewbutton.style.display = "none";
}

if ( property.folder_id_exist ) {
	showviewbutton.style.display = "none";
    showaddfile.style.display    = "block";
}


$( 'tr.file' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {
			if( $( this ).hasClass( 'grayed' ) ) {
				alert( 'You do not have permission to access this resource.' );
			} else {
				property.file_get( $( this ).data( 'fileid' ));
			}

			e.handled = true;
		}
	} );

    $( 'tr.folder' ).on( 'click', function( e ) {
		e.preventDefault();
		if ( e.handled !== true ) {
			if( $( this ).hasClass( 'grayed' ) ) {
				alert( 'You do not have permission to access this resource.' );
			} else {
				var dstfolder_id = $( this ).data( 'folderid' )
               property.subfolder_id = dstfolder_id
			   property.folder_list( dstfolder_id );
			}

			e.handled = true;
		}
	} );
  
      
</script>

<?php $content_index = ob_get_clean(); ?>

<?php ob_start(); ?>
<div class="button-header">
	<input type="button" value="Back" class="back" data-function="back-property">
	<input type="button" value="Save" class="save" data-function="save">
</div><!-- /.button-header -->
<div class="errors"></div>
%FORM_CONTENT%
<?php $content_addedit = ob_get_clean(); ?>
