<?php namespace App\Cf\Middleware;

use Closure;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;

use App\Cf\ResponseTrait;

class ApiHttpAuth {

    use ResponseTrait;

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
        /*
         * TODO: This line says that we don't need a session here. And this causes some errors. Commenting for now.
         */
//        Config::set('session.driver', 'array');

        if (!$request->headers->get('PHP_AUTH_USER') || !$request->headers->get('PHP_AUTH_PW')) {
            $headers = ['WWW-Authenticate' => 'Basic'];
            return $this->returnJsonResponse('0', 'Invalid credentials.', 401, $headers);
        }

        $apiUser = Config::get('cf.api_user');
        $apiPassword = Config::get('cf.api_password');
        if ($request->headers->get('PHP_AUTH_USER') != $apiUser || $request->headers->get('PHP_AUTH_PW') != $apiPassword) {
            $headers = ['WWW-Authenticate' => 'Basic'];
            return $this->returnJsonResponse('0', 'Invalid credentials.', 401, $headers);        }

        return $next($request);
	}

}
