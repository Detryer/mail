<?php

namespace Application\Models;

use Application\Core\Model;

class Model_Folder extends Model
{
	public function get_data()
	{
		return
			['folders' =>
				[
					['address' => 'newFolder', 'name' => 'Новая почта'],
					['address' => 'oldFolder', 'name' => 'Старая почта']
				]
			];
	}
}