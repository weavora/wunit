<?php

/**
 * Created by JetBrains PhpStorm.
 * User: USER
 * Date: 9/13/11
 * Time: 1:32 PM
 * To change this template use File | Settings | File Templates.
 */

namespace WUnit\Http;

class YiiRequest extends \CHttpRequest
{

	public function inject($files = array())
	{

		$_FILES = $this->filterFiles($files);
		if (!empty($files)) {
			var_dump($files, $_FILES);
			die();
		}
		if (empty($_SERVER['PHP_SELF'])) {
			$_SERVER['PHP_SELF'] = '/index.php';
		}

		if (empty($_SERVER['SCRIPT_FILENAME'])) {
			$_SERVER['SCRIPT_FILENAME'] = \Yii::getPathOfAlias('application') . '/../index.php';
		}
	}

	protected function normalizeRequest()
	{
		if ($this->enableCsrfValidation)
			Yii::app()->attachEventHandler('onBeginRequest', array($this, 'validateCsrfToken'));
	}

	protected function filterFiles($files, $parentKey = "")
	{
		$filtered = array();
		foreach ($files as $key => $value) {
//			$filteredKey = empty($parentKey) ? $key : $parentKey . "[" . $key . "]";
			$filteredKey = $key;
			if (is_array($value)) {
				$filtered[$filteredKey] = $this->filterFiles($value, $filteredKey);
			} elseif (is_object($value)) {
				$filtered[$filteredKey] = array(
					'tmp_name' => $value->getPathname(),
					'name' => $value->getClientOriginalName(),
					'type' => $value->getClientMimeType(),
					'size' => $value->getClientSize(),
					'error' => $value->getError(),
				);
			}
		}

		return $filtered;
	}

}

