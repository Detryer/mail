<?php

namespace Application\Models;

use Application\Core\Model;
use Application\Data\Data_Topic;
use Application;

class Model_Topic extends Model
{
	function __construct()
	{
		$this->data = new Data_Topic();
	}
	
	public function addTopic($data)
	{
		$correctData = $this->prepareAJAXPostRequest($data);
		$title = $correctData['title'];
		$text = $correctData['text'];
		$baseID = $this->data->getBaseID($correctData['base']);
		return json_encode($this->data->addTopic($title, $text, $baseID));
	}

	public function getFolders($baseName)
	{
		$folders = $this->data->getFolders($baseName);
		return $folders;
	}

	public function topicList($baseName, $folder)
	{
		$baseID = $this->data->getBaseID($baseName);
		$topics = $this->data->getTopics($baseID, $folder);
		foreach ($topics as $id => $topic) {
			$topics[$id]['text_size'] = strlen($topic['text']) > 1350 ? 'big_text' : 'small_text';
		}
		return $topics;
	}
	
	public function topicPosts($topicID)
	{
		$posts = $this->data->getPosts($topicID);
		return $posts;
	}

	public function getPostData($postID)
	{
		return $this->data->getPostData($postID);
	}

	public function editPost($topicID, $postID, $text)
	{
		$this->data->editPost($topicID, $postID, $text);
	}

	public function deleteTopic($topicID)
	{
		$this->data->deleteTopic($topicID);
	}
	
	public function trashPost($topicID)
	{
		
	}

	public function basesList()
	{
		$bases = [
			'all' => 'Все',
			'line' => 'Линия',
			'point' => 'Мастерские',
			'dv' => 'Добрые Вещи',
			'lenbaget' => 'Ленбагет',
			'lenreklama' => 'Ленреклама'
		];
		return $bases;
	}

	private function prepareAJAXPostRequest($data)
	{
		$request = [];

		foreach ($data as $item) {
			$request[$item['name']] = $item['value'];
		}
		return $request;
	}
}