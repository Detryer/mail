<?php

namespace Application\Core;

use Application;

class View
{
	private $twig;

	function generate($templateFile, $data = [])
	{
		try {
			$template = $this->twig->loadTemplate($templateFile);
			return $template->render($data);
		} catch (\Exception $e) {
			die ('ERROR: ' . $e->getMessage());
		}
	}

	function __construct()
	{
		require_once $_SERVER['DOCUMENT_ROOT'] . '/Twig/Autoloader.php';
		\Twig_Autoloader::register();
		$loader = new \Twig_Loader_Filesystem($_SERVER['DOCUMENT_ROOT'] . '/application/views');
		$this->twig = new \Twig_Environment($loader);
		$this->twig->addGlobal("VERSION", "1.0.1");
		$this->twig->addGlobal("CURRENT_URL", $_SERVER["SERVER_NAME"]);
		$this->twig->addGlobal("USER_ID", $_SESSION['user_id']);
	}
}