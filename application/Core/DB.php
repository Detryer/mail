<?php

namespace Application\Core;

use Doctrine\DBAL\Configuration;

class DB
{

	public static function connection()
	{
		$DBALConfig = new Configuration();
		$connectionParams = ['url' => 'mysql://root:cjkzhbc@127.0.0.1/mail'];
		$pdo = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $DBALConfig);
		return $pdo;
	}
	
}