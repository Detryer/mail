<?php

namespace Application\Controllers;

use Application\Core\Controller;

class Controller_401 extends Controller
{

	function action_view()
	{
		$data =
			[
				'line' => 'http://basa.lenremont.ru',
				'point' => 'http://point.lenremont.ru',
				'dv' => 'http://basa.dobrov.su'
			];
		$this->view->generate('401_view.html', $data);
	}

}