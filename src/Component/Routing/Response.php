<?php

namespace Pagekit\Component\Routing;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Response
{
    /**
     * @var UrlGenerator
     */
    protected $url;

    /**
     * Constructor.
     *
     * @param UrlGenerator $url
     */
    public function __construct(UrlGenerator $url)
    {
        $this->url = $url;
    }

	/**
	 * Returns a response.
	 *
	 * @param  mixed $content
	 * @param  int   $status
	 * @param  array $headers
	 * @return HttpResponse
	 */
	public function create($content = '', $status = 200, $headers = array())
	{
		return new HttpResponse($content, $status, $headers);
	}

	/**
	 * Returns a redirect response.
	 *
	 * @param  string  $url
	 * @param  array   $parameters
	 * @param  int     $status
	 * @param  array   $headers
	 * @return RedirectResponse
	 */
	public function redirect($url, $parameters = array(), $status = 302, $headers = array())
	{
		return new RedirectResponse($this->url->route($url, $parameters), $status, $headers);
	}

	/**
	 * Returns a JSON response.
	 *
	 * @param  string|array $data
	 * @param  int          $status
	 * @param  array        $headers
	 * @return JsonResponse
	 */
	public function json($data = array(), $status = 200, $headers = array())
	{
		return new JsonResponse($data, $status, $headers);
	}

	/**
	 * Returns a streamed response.
	 *
	 * @param  Closure $callback
	 * @param  int     $status
	 * @param  array   $headers
	 * @return StreamedResponse
	 */
	public function stream($callback, $status = 200, $headers = array())
	{
		return new StreamedResponse($callback, $status, $headers);
	}

	/**
	 * Returns a binary file download response.
	 *
	 * @param  string $file
	 * @param  string $name
	 * @param  array  $headers
	 * @return BinaryFileResponse
	 */
	public function download($file, $name = null, $headers = array())
	{
		$response = new BinaryFileResponse($file, 200, $headers, true, 'attachment');

		if (!is_null($name)) {
			$response->setContentDisposition('attachment', $name);
		}

		return $response;
	}
}
