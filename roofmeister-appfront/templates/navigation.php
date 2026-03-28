<?php

	$templates['navigation']['list'] = <<<HTML
<ul data-level="level-%LEVEL%">
%ITEMS%
</ul>

HTML;

	$templates['navigation']['item'] = <<<HTML
<li data-level="level-%LEVEL%" data-item="item-%ITEM%" class="option hover">
	<div class="icon">
		<img src="%ICON%" alt="">
	</div>
	<div class="label">
		%NAME%
	</div>
%LIST%
</li>

HTML;

?>
