<?php

namespace Application\Controllers;
use Application\Core\Controller;

class Controller_404 extends Controller
{

	function action_view()
	{
		$data['base'] = $_SESSION['base'];
		$this->view->generate('404_view.html', $data);
	}

}