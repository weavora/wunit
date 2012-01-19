<?php
/**
 * Created by JetBrains PhpStorm.
 * User: USER
 * Date: 9/13/11
 * Time: 1:32 PM
 * To change this template use File | Settings | File Templates.
 */

namespace WUnit\Http;

class YiiRequest extends \CHttpRequest {

	public function inject()
	{
		if (empty($_SERVER['PHP_SELF'])) {
			$_SERVER['PHP_SELF'] = '/index.php';
		}

		if (empty($_SERVER['SCRIPT_FILENAME'])) {
			$_SERVER['SCRIPT_FILENAME'] = \Yii::getPathOfAlias('application') . '/../index.php';
		}
	}

	protected function normalizeRequest()
	{
		if($this->enableCsrfValidation)
			Yii::app()->attachEventHandler('onBeginRequest',array($this, 'validateCsrfToken'));
	}
}


