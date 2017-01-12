<?php

namespace Application\Data;

use Application\Core\Data;
use Application\Core\DB;
use Application\Debug;
use Doctrine\DBAL\Connection;

class Data_Topic extends Data
{
    private $pdo;

    public function addTopic($title, $text, $baseID)
    {
        $topic['title'] = $title;
        $topic['base_id'] = $baseID;
        $topic['user_id'] = $_SESSION['user_id'];

        $queryBuilder = $this->pdo->createQueryBuilder();
        $this->prepareData($queryBuilder, $topic);
        $queryBuilder->insert('topic')->execute();

        $users[0] = 1; // TODO Получение списка пользователей с помощью checkbox!
        $topicID = $this->pdo->lastInsertId();
        $this->addPost($topicID, $text);
        $this->topicUsers($topicID, $users);
    }

    public function addPost($topicID, $text)
    {
        $post['topic_id'] = $topicID;
        $post['user_id'] = $_SESSION['user_id'];
        $post['text'] = $text;
        $queryBuilder = $this->pdo->createQueryBuilder();
        $this->prepareData($queryBuilder, $post);
        $queryBuilder->insert('post')->execute();
    }

    public function getFolders($baseName)
    {
        $folders = [];

        $queryBuilder = $this->pdo->createQueryBuilder();
        $queryBuilder
            ->select("folders.id, folders.name")
            ->from('folders')
            ->leftJoin('folders', 'base', 'base', 'folders.base_id = base.id')
            ->orderBy("folders.name", 'ASC');
        if ($baseName !== 'all') {
            $queryBuilder
                ->where('base.name = :base')
                ->setParameter('base', $baseName);
        }
        $result = $queryBuilder->execute();

        while ($row = $result->fetch()) {
            array_push($folders, $row);
        }

        return $folders;
    }

    public function getTopics($baseID, $folder)
    {
        $topics = [];

        $showDeleted = isset($_SESSION['mail']['show_deleted']) ? "0, 1" : 0;

        $queryBuilder = $this->pdo->createQueryBuilder();
        $queryBuilder
            ->select("topic.id, topic.title, topic.date, topic.user_id, topic.date_edit, post.text, user.avatar, user.fio")
            ->from('topic')
            ->leftJoin('topic', 'post', 'post', 'post.topic_id = topic.id')
            ->leftJoin('topic', 'user', 'user', 'user.id = topic.user_id')
            ->leftJoin('topic', 'topic_user', 'topic_user', 'topic_user.topic_id = topic.id')
            ->where('topic_user.user_id = :user')
            ->setParameter('user', $_SESSION['user_id'])
            ->andWhere('topic.deleted IN (:topic_state)')
            ->setParameter('topic_state', $showDeleted)
            ->groupBy('topic.id')
            ->orderBy("post.date", 'DESC');

        if ($baseID !== 0) {
            $queryBuilder
                ->leftJoin('topic', 'base', 'base', "base.id = topic.base_id OR topic.base_id IN(0, :base_id)")
                ->andWhere('base.id IN (0, :base_id)')
                ->setParameter('base_id', $baseID);
        }
        if ($folder !== 'all') {
            switch ($folder) {
                case 'new':
                    $queryBuilder->andWhere('topic_user.read_date IS NULL');
                    break;
                case 'my':
                    $queryBuilder->andWhere('topic.user_id = :user_id', $_SESSION['user_id']);
                    break;
                case 'read':
                    $queryBuilder->andWhere('topic_user.read_date NOT NULL');
                    break;
                case 'noanswer':
                    $queryBuilder->andWhere('topic.date = topic.date_add')->andWhere('topic.user_id = :user_id', $_SESSION['user_id']);
                    break;
                case 'reminders':
                case 'notice':
                case 'draft':
                case 'trash':
                    $queryBuilder->join('topic_trash', 'topic_trash', 'topic_trash', 'topic_trash.id = topic.id');
                    break;
                default:
                    $queryBuilder
                        ->leftJoin('topic', 'topic_folder', 'topic_folder', 'topic_folder.topic_id = topic.id')
                        ->andWhere('topic_folder.folder_id = :folder')
                        ->setParameter('folder', $folder);
                    break;
            }
        }
        $result = $queryBuilder->execute()->fetchAll();

        foreach ($result as $row) {
            $topics[$row['id']] = $row;
            $topics[$row['id']]['text_size'] = strlen($row['text']) > 1350 ? 'big_text' : 'small_text';
        }

        $topicsID = array_keys($topics);
        $postsCount = $this->postsCount($topicsID);

        foreach ($topics as $topic) {
            $topics[$topic['id']]['messages_count'] = $postsCount[$topic['id']];
        }
        return $topics;
    }

    public function newPosts()
    {
        $queryBuilder = $this->pdo->createQueryBuilder();
        $queryBuilder->select('COUNT(topic.id), folder.id')->from('topic')->leftJoin('topic', 'folders', '')->where('');
    }

    //TODO дописать
    public function topicsCount($baseName)
    {
        $topics = [];
        $baseID = $this->getBaseID($baseName);

        $queryBuilderMain = $this->pdo->createQueryBuilder();
        $queryBuilderMain
            ->select('COUNT(topic.id) as count')
            ->from('topic')
            ->leftJoin('topic', 'topic_user', 'topic_user', 'topic_user.topic_id = topic.id')
            ->where('topic_user.user_id = :user_id')
            ->setParameter('user_id', $_SESSION['user_id']);
        if ($baseID != 0) {
            $queryBuilderMain
                ->andWhere('topic.base_id IN(0, :base_id)')
                ->setParameter('base_id', $baseID);
        }

        $queryBuilder = clone $queryBuilderMain;
        $queryBuilder->andWhere('topic_user.read_date IS NULL');
        $topics['new'] = intval($queryBuilder->execute()->fetch()['count']);

        $queryBuilder = clone $queryBuilderMain;
        $queryBuilder->andWhere('topic.user_id = :user_id');
        $topics['my'] = intval($queryBuilder->execute()->fetch()['count']);

        $queryBuilder = clone $queryBuilderMain;
        $queryBuilder->andWhere('topic_user.read_date IS NOT NULL');
        $topics['read'] = intval($queryBuilder->execute()->fetch()['count']);

        $topics['all'] = $queryBuilderMain->execute()->fetch()['count'];
        return $topics;
    }

    public function basesTopicsCount()
    {
        $topicsBases = [];
        $queryBuilder = $this->pdo->createQueryBuilder();
        $queryBuilder
            ->select('COUNT(topic.id) as count, topic.base_id')
            ->from('topic')
            ->leftJoin('topic', 'topic_user', 'topic_user', 'topic_user.topic_id = topic.id')
            ->where('topic_user.user_id = :user_id')
            ->setParameter('user_id', $_SESSION['user_id'])
            ->groupBy('topic.base_id');
        $result = $queryBuilder->execute();
        while($row = $result->fetch()){
            $topicBases[$row['base_id']] = $row['count'];
        }
        Debug::r($topicsBases);
    }

    private function postsCount($topicsID)
    {
        $postsCount = [];

        $queryBuilder = $this->pdo->createQueryBuilder();
        $queryBuilder
            ->select('COUNT(post.id) as count, post.topic_id')
            ->from('post')
            ->where('post.topic_id IN (:topics)')
            ->setParameter('topics', $topicsID, Connection::PARAM_INT_ARRAY)
            ->groupBy('post.topic_id');
        $result = $queryBuilder->execute()->fetchAll();

        foreach ($result as $row) {
            $postsCount[$row['topic_id']] = $row['count'];
        }
        return $postsCount;
    }

    public function getPosts($topicID)
    {
        $queryBuilder = $this->pdo->createQueryBuilder();
        $queryBuilder
            ->select("topic.title, post.id, post.text, post.date, user.id as user_id, user.fio")
            ->from('post')
            ->leftJoin('post', 'topic', 'topic', 'topic.id = post.topic_id')
            ->leftJoin('post', 'user', 'user', 'user.id = post.user_id')
            ->where('post.topic_id = :topic_id')
            ->setParameter('topic_id', $topicID)
            ->orderBy("post.date", 'ASC');
        $posts = $queryBuilder->execute()->fetchAll();

        return $posts;
    }

    public function deleteTopic($topicID)
    {
        $queryBuilder = $this->pdo->createQueryBuilder();
        $queryBuilder
            ->update('topic')
            ->set('topic.deleted', ':deleted')
            ->setParameter('deleted', 1)
            ->where('topic.id = :topic_id')
            ->setParameter('topic_id', $topicID);
        $queryBuilder->execute();
    }

    public function editPost($topicID, $postID, $text)
    {
        $this->pdo->update('post', ['text' => $text], ['id' => $postID]);
        $this->pdo->update('topic', ['date_edit' => time()], ['id' => $topicID]);
    }

    public function deletePost($topicID, $deleted = 1)
    {
        $queryBuilder = $this->pdo->createQueryBuilder();
        $queryBuilder
            ->update('post')
            ->set('post.deleted', ':deleted')
            ->setParameter('deleted', 1)
            ->where('topic.id = :topic_id')
            ->setParameter('topic_id', $topicID);
        $queryBuilder->execute();
    }

    private function topicUsers($topicID, $users)
    {
        $queryBuilder = $this->pdo->createQueryBuilder();
        $data['topic_id'] = $topicID;

        foreach ($users as $user) {
            $queryBuilder
                ->values(
                    [
                        'topic_id' => ':topic_id',
                        'user_id' => ':user_id'
                    ]
                )
                ->setParameter('topic_id', $topicID)
                ->setParameter('user_id', $user);
            if ($user == $_SESSION['user_id']) {
                $data['read_date'] = time();
            }
            $queryBuilder->insert('topic_user')->execute();
        }
    }

    function getBaseID($baseName = '')
    {
        $bases = [
            'all' => 0,
            'line' => 1,
            'point' => 2,
            'dv' => 3,
            'lenbaget' => 4,
            'lenreklama' => 5
        ];
        return $baseName ? $bases[$baseName] : $bases;
    }

    public function getPostData($postID)
    {
        $queryBuilder = $this->pdo->createQueryBuilder();
        $queryBuilder
            ->select('post.text, post.topic_id')
            ->from('post')
            ->where('post.id = :post_id')
            ->setParameter('post_id', $postID);
        $post = $queryBuilder->execute()->fetch();
        return $post;
    }

    private function prepareData($queryBuilder, $data)
    {
        foreach ($data as $key => $value) {
            $queryBuilder
                ->setValue($key, ":$key")
                ->setParameter($key, $value);
        }
    }

    function __construct()
    {
        $this->pdo = DB::connection();
    }
}