<?php

namespace Application\Controllers;

use Application\Core\Controller;
use Application\Models\Model_Topic;
use Application\Widgets\Widget;
use Application\Debug;

class Controller_Topic extends Controller
{

	function __construct()
	{
		parent::__construct();
		$this->model = new Model_Topic();
	}

	function action_view($topicID)
	{
		$data['posts'] = $this->model->topicPosts($topicID);
		$data['topic_id'] = $topicID;
		$data['topic_title'] = $data['posts'][0]['title'];
		$data['base'] = $_SESSION['base'];
		echo $this->view->generate('post_view.html', $data);
	}

	function action_addForm()
	{
		$data['base_list'] = Widget::DropDownSingle('base', $this->model->basesList(), [
			'id' => 'base',
			'selected' => $_SESSION['base'],
			'search' => false,
			'showall' => false
		]);
		echo json_encode($this->view->generate('add_topic_view.html', $data));
	}

	function action_addTopic()
	{
		$addData = $_POST['addData'];
		$this->model->addTopic($addData);
	}

	function action_editTitle($topicID, $title)
	{

	}

	function action_editPostForm($postID){
		$data = $this->model->getPostData($postID);
		echo json_encode($this->view->generate('edit_post_view.html', $data));
	}

	function action_editPost(){
		$text = $_POST['text'];
		$postID = $_POST['postID'];
		$topicID = $_POST['topicID'];
		$this->model->editPost($postID, $topicID, $text);
	}

	function action_deleteTopic($ID)
	{
		return $this->model->deleteTopic($ID);
	}

	function action_deletePost($ID)
	{
		return $this->model->deletePost($ID);
	}
}