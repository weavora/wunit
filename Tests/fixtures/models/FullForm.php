<?php

class FullForm extends CFormModel{
	public $textField;
	public $checkBox;
	public $dropDownList;
	public $passwordField;
	public $textArea;
	public $radioButton;  //@todo trouble with initializate in Form()
	public $fileField;

	public function rules(){
		return array(
			array('textField', 'required', 'message' => 'textField are not empty'),
			array('fileField', 'file'),
			array('checkBox, dropDownList, passwordField, textArea, radioButton', 'safe'),
		);
	}
}