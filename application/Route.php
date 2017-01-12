<?php

namespace Application;
class Route
{
	static function start()
	{
		$routes = explode('/', $_SERVER['REQUEST_URI']);

		$controllerName = !empty($routes[1]) ? $routes[1] : 'List';
		$actionName = !empty($routes[2]) ? $routes[2] : 'view';

		if (isset($routes[3])) {
			preg_match('/^[a-zA-Z0-9_]*/', $routes[3], $subject);
			$subjectID = $subject[0];
		}
		
		if (!isset($_SESSION['logged_user'])) {
			$controllerName = "401";
		}
		
		$controllerName = __NAMESPACE__ . '\Controllers\Controller_' . ucfirst($controllerName);
		$actionName = 'action_' . $actionName;
		if (class_exists($controllerName)) {
			$controller = new $controllerName();
			if (method_exists($controller, $actionName)) {
				if (isset($subjectID)) {
					$controller->$actionName($subjectID);
				} else {
					$controller->$actionName();
				}
			} else {
				self::ErrorPage404();
			}
		} else {
			self::ErrorPage404();
		}
	}

	/**
	 * Страница 404
	 */
	static function ErrorPage404()
	{
		$host = 'http://' . $_SERVER['HTTP_HOST'] . '/';
		header('HTTP/1.1 404 Not Found');
		header("Status: 404 Not Found");
		header('Location:' . $host . '404');
	}

//	/**
//	 * Страница 401
//	 */
//	static function ErrorPage401()
//	{
//		$host = 'http://' . $_SERVER['HTTP_HOST'] . '/';
//		header('Location:' . $host . '401');
//	}
}