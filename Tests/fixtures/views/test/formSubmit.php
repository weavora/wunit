<div class="row">
	<?php echo $model->textField; ?>
</div>

<div class="row">
	<?php echo $model->checkBox ?  "CheckBox is checked": "Not check"; ?>
</div>

<div class="row">
	<?php echo $model->dropDownList; ?>
</div>

<div class="row">
	<?php echo $model->passwordField; ?>
</div>

<div class="row">
	<?php echo $model->textArea; ?>
</div>

<div class="row">
	<?php echo $upload_result ? $model->fileField['name'] : ""; ?>
</div>


