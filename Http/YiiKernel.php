<?php

namespace WUnit\Http;

use WUnit\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
 
class YiiKernel implements HttpKernelInterface
{
	
	public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
	{
		$request->overrideGlobals();
		$app = $this->createYiiApp();
		$app->setComponent('request',new YiiRequest());
		$app->request->inject();

		$hasError = false;

		ob_start();
		try {
			$app->processRequest();
		} catch (Exception $e) {
			$hasError = true;
		}
		
		$content = ob_get_contents();
		ob_end_clean();

		$headers = $this->getHeaders();
		return new Response($content, $this->getStatusCode($headers, $hasError), $headers);
	}

	/**
	 * @return \CWebApplication
	 */
	protected function createYiiApp()
	{
		return \Yii::app();
	}

	protected function getHeaders()
	{
		$rawHeaders = xdebug_get_headers();
		$headers = array();
		foreach($rawHeaders as $rawHeader) {
			list($name, $value) = explode(":", $rawHeader, 2);
			$headers[strtolower(trim($name))] = trim($value);
		}
		return $headers;
	}

	protected function getStatusCode($headers, $error = false)
	{
		if ($error)
			return 503;

		if (array_key_exists('location', $headers))
			return 302;

		return 200;
	}
}
