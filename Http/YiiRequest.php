<?php
/**
 * @author Weavora Team <hello@weavora.com>
 * @link http://weavora.com
 * @copyright Copyright (c) 2011 Weavora LLC
 */

namespace WUnit\Http;

use Symfony\Component\HttpFoundation\Request;

class YiiRequest extends \CHttpRequest
{
    private $content;

    public function __construct(Request $request)
    {
        $this->content = $request->getContent();
    }

    public function inject(array $files = array())
    {
        $_FILES = $this->filterFiles($files);
        if (empty($_SERVER['PHP_SELF'])) {
            $_SERVER['PHP_SELF'] = '/index.php';
        }

        if (empty($_SERVER['SCRIPT_FILENAME'])) {
            $_SERVER['SCRIPT_FILENAME'] = \Yii::getPathOfAlias('application') . '/../index.php';
        }
    }

    public function getRawBody()
    {
        return $this->content;
    }

    protected function normalizeRequest()
    {
        if ($this->enableCsrfValidation) {
            Yii::app()->attachEventHandler('onBeginRequest', array($this, 'validateCsrfToken'));
        }
    }

    protected function filterFiles($files)
    {
        $filtered = array();
        foreach ($files as $key => $value) {
            if (is_array($value) && !empty($value)) {
                $keyData = $this->filterFiles($value);
                if (!empty($keyData)) {
                    $filtered[$key] = $keyData;
                }
            } elseif (is_object($value)) {
                // Yii style :)
                $filtered['tmp_name'][$key] = $value->getPathname();
                $filtered['name'][$key] = $value->getClientOriginalName();
                $filtered['type'][$key] = $value->getClientMimeType();
                $filtered['size'][$key] = $value->getClientSize();
                $filtered['error'][$key] = $value->getError();
            }
        }

        return $filtered;
    }
}
