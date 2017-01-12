<?php

namespace Application\Models;

use Application\Core\Model;
use Application\Data\Data_Topic;
use Application;
use Application\Debug;

class Model_List extends Model
{
	function __construct()
	{
		$this->data = new Data_Topic();
	}

	public function getFolders($baseName)
	{
		return $this->data->getFolders($baseName);
	}

	public function topicsCount($baseName)
	{
		return $this->data->topicsCount($baseName);
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
		return $this->data->getPosts($topicID);
	}

	public function basesTopicsCount()
	{
		return $this->data->basesTopicsCount();
	}

	public function deleteTopic($ID)
	{
//		$queryBuilder = new QueryBuilder();
//		$queryBuilder->delete('topic', $ID);
	}
}