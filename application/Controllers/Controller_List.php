<?php

namespace Application\Controllers;

use Application\Core\Controller;
use Application\Models\Model_List;
use Application\Debug;

class Controller_List extends Controller
{

	function __construct()
	{
		parent::__construct();
		$this->model = new Model_List();
	}

	function action_view($baseName = 'all')
	{
		$folder = isset($_GET['folder']) ? $_GET['folder'] : 'all';
		$data['base'] = $_SESSION['base'];
		$data['topics'] = $this->model->topicList($baseName, $folder);
		$data['folders'] = $this->model->getFolders($baseName);
		$data['topics_count'] = $this->model->topicsCount($baseName);
		$data['bases_topics_count'] = $this->model->basesTopicsCount();
		echo $this->view->generate('topic_view.html', $data);
	}
}