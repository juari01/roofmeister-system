<?php ob_start(); ?>
<div class="button-header">
<input type="button" name="add-file" value="%ADD_FILE%" data-function="add-file"> 
<input type="button" name="add-folder" value="%ADD_FOLDER%" data-function="add-folder"> 
</div><!-- /.button-header -->
 
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
</div><!-- /.add-file -->

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
<div class="search search-folder" style="margin-top: 5px;" >
    <input type="text" placeholder="" name="search_text">
    <input type="button" value="Search" data-function="search-folder">
</div>
<br>
<div id="folder_breadcrumbs">
    %BREADCRUMBS_LIST%
</div>
<div class="files_table_content">
    %FILES_LIST%
</div>


<?php $content_index = ob_get_clean(); ?>

<?php ob_start(); ?>
<div class="permissions-popup">
    <div class="content" style="overflow: auto;">
        %%PERMISSIONS%%
    </div>
</div>


<?php $permissions_popup_view = ob_get_clean(); ?>