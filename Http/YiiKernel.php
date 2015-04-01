<?php
/**
 * @author Weavora Team <hello@weavora.com>
 * @link http://weavora.com
 * @copyright Copyright (c) 2011 Weavora LLC
 */

namespace WUnit\Http;

use WUnit\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class YiiKernel implements HttpKernelInterface
{
    /**
     * @param Request $request
     * @param integer $type
     * @param boolean $catch
     *
     * @return Response
     */
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        $request->overrideGlobals();
        $app = \Yii::app();
        $app->setComponent('request', new YiiRequest($request));
        $app->request->inject($request->files->all());

        $hasError = false;
        $statusCode = null;

        ob_start();
        try {
            $app->processRequest();
        } catch (YiiExitException $exc) {

        } catch (\CHttpException $exc) {
            $statusCode = $exc->statusCode;
            $hasError = true;
        } catch (\Exception $exc) {
            $hasError = true;
        }

        $content = ob_get_contents();
        ob_end_clean();

        $headers = $this->getHeaders();

        $sessionId = session_id();
        if (empty($sessionId)) {
            session_regenerate_id();
            $app->session->open();
        }

        return new Response($content, $statusCode ?: $this->getStatusCode($headers, $hasError), $headers);
    }

    /**
     * @return array
     */
    protected function getHeaders()
    {
        $rawHeaders = xdebug_get_headers();
        $headers = array();
        foreach ($rawHeaders as $rawHeader) {
            list($name, $value) = explode(":", $rawHeader, 2);
            $name = strtolower(trim($name));
            $value = trim($value);
            if (!isset($headers[$name])) {
                $headers[$name] = array();
            }

            $headers[$name][] = $value;
        }

        return $headers;
    }

    /**
     * @param array   $headers
     * @param boolean $error
     *
     * @return integer
     */
    protected function getStatusCode($headers, $error = false)
    {
        if ($error)
            return 503;

        if (array_key_exists('location', $headers))
            return 302;

        return 200;
    }
}
