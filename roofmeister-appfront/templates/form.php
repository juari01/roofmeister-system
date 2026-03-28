<?php

// Format Form Template
	$form_templates = [];

	$form_templates['main_form']['autosuggest'] = <<<HTML
<div class="input autocomplete %CLASS%" %DATA%>
	<span class="label">%LABEL%</span>
	<span class="input"><input type="text" name="%NAME%" placeholder="%PLACEHOLDER%" value="%VALUE%" %DATA%></span>
</div>
<script type="text/javascript">
	$( 'input[name=%NAME%]' ).autocomplete( %PARAMS% ).on( 'keydown', function( ev ) {
		if ( ev.keyCode == 13 ) {
			ev.preventDefault();
			return false;
		}
	} );
</script>
HTML;

	$form_templates['main_form']['tinymce'] = <<<HTML
<div class="input textarea %CLASS%" %DATA%>
	<span class="label">%LABEL%</span>
	<span class="input %CLASS%">
		<textarea name="%NAME%">%VALUE%</textarea>
	</span>
	<script type="text/javascript"> $( 'textarea[name="%NAME%"]' ).tinymce({ %PARAMS% }); </script>
</div>
HTML;
	$default_tinymce_params = <<<JS
	theme: 'modern',
	plugins: [
		'advlist autolink lists link image charmap print preview hr anchor pagebreak',
		'searchreplace wordcount visualblocks visualchars code fullscreen',
		'insertdatetime media nonbreaking save table contextmenu directionality',
		'emoticons template paste textcolor colorpicker textpattern imagetools codesample'
	],
	toolbar1: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
	toolbar2: 'print preview media | forecolor backcolor emoticons | codesample',
	image_advtab: true,
JS;

	$form_templates['main_form']['button'] = <<<HTML
<div class="input button %CLASS%" %DATA% %ATTR%>
	<span class="input">
		<input type="button" name="%NAME%" value="%VALUE%" data-id="%DATA_ID%" class="%CLASS%" %DATA% %DISABLED%>
	</span>
</div>
HTML;

	$form_templates['main_form']['checkbox'] = <<<HTML
<div class="input checkbox %CLASS%" %DATA%>
	<span class="label">%LABEL%</span>
	<span class="input">
		%%OPTIONS%%
		<span class="check"><input type="checkbox" name="%NAME%" value="%VALUE%" %ATTR% %DATA% %CHECKED% %DISABLED% >%DISPLAY%</span>
		%%OPTIONS%%
	</span>
</div>
HTML;

	$form_templates['main_form']['container'] = <<<HTML
<div id="%ID%" class="%CLASS%" %DATA% %ATTR%>%HTML%</div>
HTML;

	$form_templates['main_form']['hidden'] = <<<HTML
<input type="hidden" name="%NAME%" value="%VALUE%" class="%CLASS%" %DATA%>
HTML;

	$form_templates['main_form']['hr'] = <<<HTML
<hr>
HTML;

	$form_templates['main_form']['linebreak'] = <<<HTML
<hr class="linebreak %CLASS%">
HTML;

	$form_templates['main_form']['multicheck'] = <<<HTML
<div class="input multicheck %CLASS%" %DATA%>
	<span class="label">%LABEL%</span>
	<span class="input">
	%%OPTIONS%%
		<span>
			<input type="checkbox" name="%NAME%" value="%VALUE%" %DATA% %CHECKED%>%DISPLAY%
		</span>
	%%OPTIONS%%
	</span>
</div>
HTML;

	$form_templates['main_form']['parent-container'] = <<<HTML
<div class="%CLASS%" id="%ID%" %DATA%>
%CHILDREN%
</div>
HTML;

	$form_templates['main_form']['password'] = <<<HTML
<div class="input password %CLASS%" %DATA%>
	<span class="label">%LABEL%</span>
	<span class="input">
		<input type="password" name="%NAME%" placeholder="%PLACEHOLDER%" value="%VALUE%" %DATA%>
	</span>
</div>
HTML;

	$form_templates['main_form']['radio'] = <<<HTML
<div class="input radio %CLASS%" %DATA%>
	<span class="label">%LABEL%</span> <br><br>
	<span class="input">
	%%OPTIONS%%
		<input type="radio" name="%NAME%" id="%ID%" value="%VALUE%" %DATA% %CHECKED% %DISABLED%>%DISPLAY%
	%%OPTIONS%%
	</span>
</div>
HTML;

	$form_templates['main_form']['select'] = <<<HTML
<div class="input select %CLASS%" %DATA%>
	<span class="label">%LABEL%</span>
	<span class="input">
		<select name="%NAME%" %DATA% %DISABLED%>
		%%OPTIONS%%
			<option value="%VALUE%" %DATA% %SELECTED%>%DISPLAY%</option>
		%%OPTIONS%%
		</select>
	</span>
</div>
HTML;
$form_templates['main_form']['chosen'] = <<<HTML
<div class="input select chosen %CLASS%" %DATA% %ATTR%>
	<span class="label">%LABEL%</span>
	<span class="input %CLASS%">
		<select %MULTIPLE% name="%NAME%" %DATA% %DISABLED%>
		%%OPTIONS%%
			<option value="%VALUE%" %DATA% %SELECTED%>%DISPLAY%</option>
		%%OPTIONS%%
		</select>
	</span>
</div>
<script type="text/javascript">$( 'select[name=%NAME%]' ).chosen( %PARAMS% );</script>
HTML;

	$form_templates['main_form']['chosen-optgroup'] = <<<HTML
<div class="input select chosen %CLASS%" %DATA% %ATTR%>
	<span class="label">%LABEL%</span>
	<span class="input %CLASS%">
		<select %MULTIPLE% name="%NAME%" %DATA% %DISABLED%>
		%%OPTIONS%%
			<option value="%VALUE%" %DATA% %SELECTED%>%DISPLAY%</option>
		%%OPTIONS%%
		</select>
	</span>
</div>
<script type="text/javascript">$( 'select[name=%NAME%]' ).chosen( %PARAMS% );</script>
HTML;

	$form_templates['main_form']['textarea'] = <<<HTML
<div class="input textarea %CLASS%" %DATA%>
	<span class="label">%LABEL%</span>
	<span class="input">
		<textarea name="%NAME%" placeholder="%PLACEHOLDER%" %DATA% %ATTR% %WRAP% %READONLY%>%VALUE%</textarea>
	</span>
</div>
HTML;

	$form_templates['main_form']['text'] = <<<HTML
<div class="input text %CLASS%" %DATA%>
	<span class="label">%LABEL%%TOOLTIP%</span>
	<span class="input">
		<input type="text" name="%NAME%" placeholder="%PLACEHOLDER%" value="%VALUE%" id="%ID%" %DATA% %ATTR% %DISABLED% %READONLY%>
	</span>
</div>
HTML;

$form_templates['main_form']['color'] = <<<HTML
<div class="input color %CLASS%" %DATA%>
	<span class="label">%LABEL%</span> <br>
	<span class="input">
		<input type="color" name="%NAME%" value="%VALUE%" id="%ID%" %DATA% width="200px" style="width:200px;">
	</span>
</div>
HTML;


	$form_templates['main_form']['static'] = <<<HTML
<div class="input static %CLASS%" title="%TITLE%" %DATA%>
	<span class="label">%LABEL%</span>
	<span class="input" %ATTR%>%VALUE%</span>
</div>
HTML;

	$form_templates['main_form']['tooltip'] = <<<HTML
<span class="tooltip"><img src="/images/icon_tooltip.png" alt="Tooltip"><div class="tooltip-container"><span class="text">%TOOLTIP%</span></div></span>
HTML;

	$form_templates['main_form']['file'] = <<<HTML
<div class="input file %CLASS%" %DATA%>
	<span class="label">%LABEL%</span>
	<span class="input"><input type="file" name="%NAME%" %MULTIPLE% id="%ID%"></span>
</div>
HTML;

	$form_templates['main_form']['datetimepicker'] = <<<HTML
<div class="input datetimepicker %CLASS%" %DATA%>
	<span class="label">%LABEL%</span>
	<span class="input"><input type="text" name="%NAME%" placeholder="%PLACEHOLDER%" value="%VALUE%" %DATA%></span>
</div>
<script type="text/javascript">$( 'input[name="%NAME%"]' ).datetimepicker( %PARAMS% );</script>
HTML;
	$form_templates['main_form']['datetimepicker_default_params'] = <<<JS
{
	timepicker: false,
	format: 'd/n/Y',
	closeOnDateSelect:true
}
JS;

	$form_templates['main_form']['daterangepicker'] = <<<HTML
<div class="input %CLASS%" %DATA%>
	<span class="label">%LABEL%</span>
	<span class="input"><input type="text" name="%NAME%" placeholder="%PLACEHOLDER%" value="%VALUE%" %DATA%></span>
</div>
<script type="text/javascript">
	$( 'input[name="%NAME%"]' ).daterangepicker( %PARAMS% );
	$( 'input[name="%NAME%"]' ).on( 'apply.daterangepicker', function( ev, picker ) {
      $( this ).val( picker.startDate.format( 'DD/MM/YYYY' ) + ' - ' + picker.endDate.format( 'DD/MM/YYYY' ) );
  	} );
</script>
HTML;
	$form_templates['main_form']['daterangepicker_default_params'] = <<<JS
{
	timepicker: false,
	opens: 'left',
	autoUpdateInput: false,
	locale: {
      format: 'DD/MM/YYYY',
	  cancelLabel: 'Clear'
    }
}
JS;

	$form_templates['main_form']['number'] = <<<HTML
<div class="input text %CLASS%" %DATA%>
	<span class="label">%LABEL%%TOOLTIP%</span>
	<span class="input">
		<input type="number" name="%NAME%" value="%VALUE%" %DATA% %ATTR%  %DISABLED%>
	</span>
</div>
HTML;

// Popup form templates

	$form_templates['popup_form']['checkbox'] = <<<HTML
<div class="input checkbox %CLASS%" %DATA% %ATTR%>
	<span class="label">%LABEL%</span>
	<span class="input">
	%%OPTIONS%%
		<label><input type="checkbox" name="%NAME%" value="%VALUE%" %ATTR% %DATA% %CHECKED% %DISABLED%>%DISPLAY%</label><br>
	%%OPTIONS%%
	</span>
</div>
HTML;

	$form_templates['popup_form']['button'] = <<<HTML
<div class="input button %CLASS%" %DATA%>
	<span class="input"><input type="button" name="%NAME%" value="%VALUE%" class="%CLASS%" %DATA% %DISABLED%></span>
</div>
HTML;

	$form_templates['popup_form']['hr'] = <<<HTML
<hr>
HTML;

	$form_templates['popup_form']['linebreak'] = <<<HTML
<hr class="linebreak %CLASS%">
HTML;

	$form_templates['popup_form']['hidden'] = <<<HTML
<input type="hidden" name="%NAME%" value="%VALUE%" class="%CLASS%" %DATA%>
HTML;

	$form_templates['popup_form']['container'] = <<<HTML
<div class="%CLASS%" id="%ID%" %DATA% %ATTR%>%HTML%</div>
HTML;

	$form_templates['popup_form']['multicheck'] = <<<HTML
<div class="input multicheck %CLASS%" %DATA%>
	<span class="label">%LABEL%</span>
	<span class="input">
		"%%OPTIONS%%
		<span><input type="checkbox" name="%NAME%" value="%VALUE%" %DATA% %CHECKED%>%DISPLAY%</span>
		%%OPTIONS%%
	</span>
</div>
HTML;

	$form_templates['popup_form']['parent-container'] = <<<HTML
<div class="%CLASS%" id="%ID%" %DATA%>
	%CHILDREN%
</div>
HTML;

	$form_templates['popup_form']['password'] = <<<HTML
<div class="input password %CLASS%" %DATA%>
	<span class="label">%LABEL%</span>
	<span class="input">
		<input type="password" name="%NAME%" placeholder="%PLACEHOLDER%" value="%VALUE%" %DATA%>
	</span>
</div>
HTML;

	$form_templates['popup_form']['radio'] = <<<HTML
<div class="input radio %CLASS%" %DATA%>
	<span class="label">%LABEL%</span>
	<span class="input">
	%%OPTIONS%%
		<input type="radio" name="%NAME%" value="%VALUE%" %DATA% %CHECKED%>%DISPLAY%
	%%OPTIONS%%
	</span>
</div>
HTML;

	$form_templates['popup_form']['select'] = <<<HTML
<div class="input select" %DATA%>
	<span class="label">%LABEL%</span>
	<span class="input">
		<select name="%NAME%" class="%CLASS%" %DATA%>
		%%OPTIONS%%
			<option value="%VALUE%" %DATA% %SELECTED%>%DISPLAY%</option>
		%%OPTIONS%%
		</select>
	</span>
</div>
HTML;

	$form_templates['popup_form']['textarea'] = <<<HTML
<div class="input textarea" %DATA% %ATTR%>
	<span class="label %CLASS%">%LABEL%</span>
	<span class="input %CLASS%"><textarea name="%NAME%" placeholder="%PLACEHOLDER%" %DATA% %ATTR% %WRAP% %READONLY%>%VALUE%</textarea></span>
</div>
HTML;

	$form_templates['popup_form']['text'] = <<<HTML
<div class="input text" %DATA%>
	<span class="label">%LABEL%</span>
	<span class="input %CLASS%"><input type="text" name="%NAME%" placeholder="%PLACEHOLDER%" value="%VALUE%" %ATTR% %DATA%></span>
</div>
HTML;

	$form_templates['popup_form']['number'] = <<<HTML
<div class="input text" %DATA%>
	<span class="label">%LABEL%</span>
	<span class="input %CLASS%">
		<input type="number" name="%NAME%" value="%VALUE%" %DATA% %ATTR%>
	</span>
</div>
HTML;

	$form_templates['popup_form']['static'] = <<<HTML
<div class="input static" title="%TITLE%" %DATA%>
	<span class="label">%LABEL%</span>
	<span class="input %CLASS%" %ATTR%>%VALUE%</span>
</div>
HTML;

	$form_templates['popup_form']['file'] = <<<HTML
<div class="input file" %DATA%>
	<span class="label">%LABEL%</span>
	<span class="input %CLASS%"><input type="file" name="%NAME%" id="%ID%" %DATA%></span>
</div>
HTML;

	$form_templates['popup_form']['chosen'] = <<<HTML
<div class="input chosen" %DATA%>
	<span class="label">%LABEL%</span>
	<span class="input %CLASS%">
		<select %MULTIPLE% name="%NAME%" %DATA%>
		%%OPTIONS%%
			<option value="%VALUE%" %SELECTED%>%DISPLAY%</option>
		%%OPTIONS%%
		</select>
	</span>
</div>
<script type="text/javascript">$( 'select[name=%NAME%]' ).chosen( %PARAMS% );</script>
HTML;

	$form_templates['popup_form']['datetimepicker'] = <<<HTML
<div class="input datetimepicker" %DATA%>
	<span class="label">%LABEL%</span>
	<span class="input %CLASS%"><input type="text" name="%NAME%" placeholder="%PLACEHOLDER%" value="%VALUE%" %DATA%></span>
</div>
<script type="text/javascript">$( 'input[name="%NAME%"]' ).datetimepicker( %PARAMS% );</script>
HTML;
	$form_templates['popup_form']['datetimepicker_default_params'] = <<<JS
{
	timepicker: false,
	format: 'd/n/Y',
	closeOnDateSelect:true
}
JS;

	$form_templates['popup_form']['daterangepicker'] = <<<HTML
<div class="input %CLASS%" %DATA%>
	<span class="label">%LABEL%</span>
	<span class="input"><input type="text" name="%NAME%" placeholder="%PLACEHOLDER%" value="%VALUE%" %DATA%></span>
</div>
<script type="text/javascript">
	$( 'input[name="%NAME%"]' ).daterangepicker( %PARAMS% );
	$( 'input[name="%NAME%"]' ).on( 'apply.daterangepicker', function( ev, picker ) {
      $( this ).val( picker.startDate.format( 'DD/MM/YYYY' ) + ' - ' + picker.endDate.format( 'DD/MM/YYYY' ) );
  	} );
</script>
HTML;
	$form_templates['main_form']['daterangepicker_default_params'] = <<<JS
{
	timepicker: false,
	opens: 'left',
	autoUpdateInput: false,
	locale: {
      format: 'DD/MM/YYYY',
	  cancelLabel: 'Clear'
    }
}
JS;

	$form_templates['popup_form']['tinymce'] = <<<HTML
<div class="input textarea" %DATA%>
	<span class="label">%LABEL%</span>
	<span class="input %CLASS%">
		<textarea name="%NAME%">%VALUE%</textarea>
		<input name=image type=file id="upload" onchange="" class="hide">
	</span>
	<script type="text/javascript"> $( 'textarea[name="%NAME%"]' ).tinymce({ %PARAMS% }); </script>
</div>
HTML;
	$default_tinymce_params = <<<JS
	theme: "modern",
	paste_data_images: true,
	plugins: [
		"advlist autolink lists link image charmap print preview hr anchor pagebreak",
		"searchreplace wordcount visualblocks visualchars code fullscreen",
		"insertdatetime media nonbreaking save table contextmenu directionality",
		"emoticons template paste textcolor colorpicker textpattern"
	],
	toolbar1: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
	toolbar2: "print preview media | forecolor backcolor emoticons",
	image_advtab: true,
	file_picker_callback: function(callback, value, meta) {
		if (meta.filetype == 'image') {
			$( '#upload' ).trigger( 'click' );
			$( '#upload' ).on( 'change', function() {
				var file = this.files[0];
				var reader = new FileReader();
				reader.onload = function( e ) {
					callback( e.target.result, {
						alt: ''
					} );
				};
				reader.readAsDataURL(file);
			} );
		}
	}
JS;

	$form_templates['popup_form']['chosen-optgroup'] = <<<HTML
<div class="input select chosen %CLASS%" %DATA% %ATTR%>
	<span class="label">%LABEL%</span>
	<span class="input">
		<select %MULTIPLE% name="%NAME%" %DATA% %DISABLED%>
		%%OPTIONS%%
			<option value="%VALUE%" %DATA% %SELECTED%>%DISPLAY%</option>
		%%OPTIONS%%
		</select>
	</span>
</div>
<script type="text/javascript">$( 'select[name=%NAME%]' ).chosen( %PARAMS% );</script>
HTML;

	$form_templates['trip_manager']['select'] = <<<HTML
<select name="%NAME%" %DATA%>
	%%OPTIONS%%
		<option value="%VALUE%" %DATA% %SELECTED%>%DISPLAY%</option>
	%%OPTIONS%%
</select>
HTML;

?>
