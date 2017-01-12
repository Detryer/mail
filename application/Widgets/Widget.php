<?php

/**
 * @todo фактори для виджет классов
 */

namespace Application\Widgets;

class Widget
{
	/**
	 * [$placeholder description]
	 * @var null
	 */
	static private $placeholder = null;

	/**
	 * [DropDownTree description]
	 * @param [type] $name    [description]
	 * @param array  $list    [description]
	 * @param array  $options [description]
	 */
	static public function DropDownTree($name, array $list, array $options = array())
	{
		$dropdownBlock = self::uniqueId();

		if (isset($options['selected']) && !is_array($options['selected'])) {
			$options['selected'] = explode(',', $options['selected']);
		}

		$attr = [
			'name' => empty($name) ? 'default' : $name,
			'list' => $list,
			'id' => isset($options['id']) ? $options['id'] : uniqid($name),
			'placeholder' => isset($options['placeholder']) ? $options['placeholder'] : 'Не выбраны',
			'selected' => isset($options['selected']) ? $options['selected'] : [],
			'search' => isset($options['search']) ? $options['search'] : true,
			'style' => isset($options['style']) ? $options['style'] : null
		];

		$searchBlock = null;
		$searchStack = self::uniqueId();
		$blockId = self::uniqueId();

		if ($attr['search'] === true) {
			$searchBlock = '<div class="dropdown-searchbox">
								<input class="form-control input-sm search-input" type="text" onkeyup="liveSearch(this, \''. $searchStack .' li\')" placeholder="поиск в списке">
							</div>';
		}

		$html = '<div id="'. $attr['id'] .'" class="btn-group blue-base-group">
					<input name="'. $attr['name'] .'" type="hidden" value="'. implode(',', $attr['selected']) .'" />
					<button class="btn btn-default btn-xs" type="button" data-dropdown="'. $dropdownBlock .'" data-toggle="dropdown" data-placeholder="'. $attr['placeholder'] .'">
						<span class="title">'. $attr['placeholder'] .'</span>
						<span class="caret"></span>
					</button>
					<div id="dropdown-'. $dropdownBlock .'" data-treeblock="'. $blockId .'" class="dropdown-menu" style="'. $attr['style'] .'">
						'. $searchBlock .'
						<div id="'. $blockId .'" class="dropdown-ul '. $searchStack .'">
							'. static::PrepareTree($attr['list']) .'
						</div>
					</div>
				</div>';

		$html .= '<script type="text/javascript">
					createTreeList(\''. $attr['id'] .'\', \''. $blockId .'\');
				</script>';

		return $html;
	}

	/**
	 * [DropDownTree description]
	 * @param [type] $name    [description]
	 * @param array  $list    [description]
	 * @param array  $options [description]
	 */
	static public function DropDownTreeSingle($name, array $list, array $options = array())
	{
		$dropdownBlock = self::uniqueId();

		if (isset($options['selected']) && !is_array($options['selected'])) {
			$options['selected'] = explode(',', $options['selected']);
		}

		$attr = [
			'name' => empty($name) ? 'default' : $name,
			'list' => $list,
			'id' => isset($options['id']) ? $options['id'] : uniqid($name),
			'placeholder' => isset($options['placeholder']) ? $options['placeholder'] : 'Не выбраны',
			'selected' => isset($options['selected']) ? $options['selected'] : [],
			'search' => isset($options['search']) ? $options['search'] : true,
			'style' => isset($options['style']) ? $options['style'] : null
		];

		$searchBlock = null;
		$searchStack = self::uniqueId();
		$blockId = self::uniqueId();

		if ($attr['search'] === true) {
			$searchBlock = '<div class="dropdown-searchbox">
								<input class="form-control input-sm search-input" type="text" onkeyup="liveSearch(this, \''. $searchStack .' li\')" placeholder="поиск в списке">
							</div>';
		}

		$html = '<div id="'. $attr['id'] .'" class="btn-group blue-base-group">
					<input name="'. $attr['name'] .'" type="hidden" value="'. implode(',', $attr['selected']) .'" />
					<button class="btn btn-default btn-xs" type="button" data-dropdown="'. $dropdownBlock .'" data-toggle="dropdown" data-placeholder="'. $attr['placeholder'] .'">
						<span class="title">'. $attr['placeholder'] .'</span>
						<span class="caret"></span>
					</button>
					<div id="dropdown-'. $dropdownBlock .'" data-treeblock="'. $blockId .'" class="dropdown-menu" style="'. $attr['style'] .'">
						'. $searchBlock .'
						<div id="'. $blockId .'" class="dropdown-ul '. $searchStack .'">
							'. static::PrepareTree($attr['list']) .'
						</div>
					</div>
				</div>';

		$html .= '<script type="text/javascript">
					createTreeList(\''. $attr['id'] .'\', \''. $blockId .'\', \'single\');
				</script>';

		return $html;
	}

	/**
	 * [DropDownMulti description]
	 * @param [type] $name    [description]
	 * @param array  $list    [description]
	 * @param array  $options [description]
	 */
	static public function DropDownMulti($name, array $list, array $options = array())
	{
		if (isset($options['selected']) && !is_array($options['selected'])) {
			$options['selected'] = explode(',', $options['selected']);
		}

		return static::DropDown([
			'name' => empty($name) ? 'default' : $name,
			'list' => $list,
			'onchange' => isset($options['onchange']) ? $options['onchange'] : 'selectMulti',
			'id' => isset($options['id']) ? $options['id'] : self::uniqueId(),
			'placeholder' => isset($options['placeholder']) ? $options['placeholder'] : 'Не выбраны',
			'selected' => isset($options['selected']) ? $options['selected'] : [],
			'search' => isset($options['search']) ? $options['search'] : true,
			'disabled' => isset($options['disabled']) ? $options['disabled'] : null,
			'showall' => isset($options['showall']) ? $options['showall'] : true,
			'callback' => isset($options['callback']) ? $options['callback'] : 0
		]);
	}

	/**
	 * [DropDownSingle description]
	 * @param string $name    [description]
	 * @param array  $list    [description]
	 * @param array  $options [description]
	 */
	static public function DropDownSingle($name, array $list, array $options = array())
	{
		if (isset($options['selected']) && !is_array($options['selected'])) {
			$options['selected'] = explode(',', $options['selected']);
		}

		return static::DropDown([
			'name' => empty($name) ? 'default' : $name,
			'list' => $list,
			'onchange' => isset($options['onchange']) ? $options['onchange'] : 'selectSingle',
			'id' => isset($options['id']) ? $options['id'] : self::uniqueId(),
			'placeholder' => isset($options['placeholder']) ? $options['placeholder'] : 'Не выбраны',
			'selected' => isset($options['selected']) ? $options['selected'] : [],
			'search' => isset($options['search']) ? $options['search'] : true,
			'disabled' => isset($options['disabled']) ? $options['disabled'] : null,
			'showall' => isset($options['showall']) ? $options['showall'] : true,
			'callback' => isset($options['callback']) ? $options['callback'] : 0
		]);
	}

	/**
	 * [DatePicker description]
	 * @param [type] $name    [description]
	 * @param array  $options [description]
	 */
	static public function DatePicker($name, array $options = array())
	{
		$blockId = self::uniqueId();

		$attr = [
			'id' => isset($options['id']) ? $options['id'] : self::uniqueId(),
			'block' => isset($options['block']) ? $options['block'] : $blockId,
			'value' => isset($options['value']) ? $options['value'] : null,
			'range' => isset($options['range']) && $options['range'] == true ? 'createDateRangePicker' : 'createDatePicker'
		];

		if ($attr['range'] == 'createDatePicker' && is_null($attr['value'])) {
			$date = new datetime('midnight');
			$attr['value'] = $date->getTimestamp();
		}

		$html = '<div id="'. $attr['block'] .'" class="form-control input-sm date-picker">
					<input id="'. $attr['id'] .'" name="'. $name .'" type="hidden" value="'. $attr['value'] .'" />
					<i class="glyphicon glyphicon-calendar"></i>
					<span></span> <b class="caret"></b>
				</div>';

		$html .= '<script type="text/javascript">
					'. $attr['range'] .'(\''. $attr['block'] .'\');
				</script>';

		return $html;
	}

	/**
	 * [TimePicker description]
	 * @param [type] $name    [description]
	 * @param array  $options [description]
	 */
	static public function TimePicker($name, array $options = array())
	{
		$blockId = self::uniqueId();

		$attr = [
			'id' => isset($options['id']) ? $options['id'] : self::uniqueId(),
			'value' => isset($options['value']) ? $options['value'] : null,
			'style' => isset($options['style']) ? $options['style'] : null
		];

		$html = '<div id="'. $blockId .'" class="time-picker">
					<input id="'. $attr['id'] .'" name="'. $name .'" type="text" value="'. $attr['value'] .'" class="form-control input-sm time-picker-hover" />
					<i class="glyphicon glyphicon-time"></i>
					<span></span> <b class="caret"></b>
				</div>';

		$html .= '<script type="text/javascript">
					createTimePicker(\''. $blockId .'\');
				</script>';		

		return $html;
	}

	/**
	 * [Input description]
	 * @param [type] $name    [description]
	 * @param array  $options [description]
	 */
	static public function Input($name, array $options = array())
	{
		$attr = [
			'id' => isset($options['id']) ? $options['id'] : self::uniqueId(),
			'type' => isset($options['type']) ? $options['type'] : 'text',
			'value' => isset($options['value']) ? $options['value'] : null,
			'placeholder' => isset($options['placeholder']) ? $options['placeholder'] : null,
			'maxlength' => isset($options['maxlength']) ? $options['maxlength'] : null,
			'disabled' => isset($options['disabled']) ? $options['disabled'] : null,
			'style' => isset($options['style']) ? $options['style'] : null
		];

		if ($attr['disabled']) {
			$attr['disabled'] = 'disabled="disabled"';
		}

		$html = '<input 
					id="'. $attr['id'] .'"
					name="'. $name .'"
					type="'. $attr['type'] .'"
					class="form-control input-sm"
					maxlength="'. $attr['maxlength'] .'"
					style="'. $attr['style'] .'"
					value="'. $attr['value'] .'"
					'. $attr['disabled'] .'
					placeholder="'. $attr['placeholder'] .'" />';

		return $html;
	}

	/**
	 * [Button description]
	 * @param array $options [description]
	 */
	static public function Button(array $options)
	{
		$attr = [
			'id' => isset($options['id']) ? $options['id'] : self::uniqueId(),
			'placeholder' => isset($options['placeholder']) ? $options['placeholder'] : 'Кнопка',
			'class' => isset($options['class']) ? $options['class'] : 'btn-default',
			'disabled' => isset($options['disabled']) ? $options['disabled'] : null
		];

		if ($attr['disabled']) {
			$attr['disabled'] = 'disabled="disabled"';
		}

		$html = '<button id="'. $attr['id'] .'" type="button" '. $attr['disabled'] .' class="btn '. $attr['class'] .'">'. $attr['placeholder'] .'</button>';

		return $html;
	}

	static public function TextArea($name, array $options = array())
	{
		$blockId = self::uniqueId();

		$attr = [
			'id' => isset($options['id']) ? $options['id'] : self::uniqueId(),
			'placeholder' => isset($options['placeholder']) ? $options['placeholder'] : null,
			'value' => isset($options['value']) ? $options['value'] : null,
			'maxlength' => isset($options['maxlength']) ? $options['maxlength'] : null,
			'counter' => isset($options['maxlength']) && isset($options['counter']) ? true : false,
			'disabled' => isset($options['disabled']) ? $options['disabled'] : null
		];

		if ($attr['disabled']) {
			$attr['disabled'] = 'disabled="disabled"';
		}

		if ($attr['counter']) {
			$attr['counter'] = '<script type="text/javascript">
									createTextArea(\''. $blockId .'\')
								</script>';
		}

		$html = '<div id="'. $blockId .'" class="widget-group">
					<textarea id="'. $attr['id'] .'" name="'. $name .'" class="form-control" maxlength="'. $attr['maxlength'] .'" '. $attr['disabled'] .' placeholder="'. $attr['placeholder'] .'">'. $attr['value'] .'</textarea>
					<span class="form-counter"></span>
					'. $attr['counter'] .'
				</div>';

		return $html;
	}

	/**
	 * [CheckBox description]
	 * @param [type] $name    [description]
	 * @param array  $options [description]
	 */
	static public function CheckBox($name, $options = array())
	{
		$attr = [
			'id' => isset($options['id']) ? $options['id'] : self::uniqueId(),
			'text' => isset($options['text']) ? $options['text'] : null,
			'checked' => isset($options['checked']) ? $options['checked'] : false,
			'disabled' => isset($options['disabled']) ? $options['disabled'] : null
		];

		if ($attr['disabled']) {
			$attr['disabled'] = 'disabled="disabled"';
		}

		if ($attr['checked']) {
			$attr['checked'] = 'checked="checked"';
		}

		$html = '<div>
					<label><input id="'. $attr['id'] .'" name="'. $name .'" type="checkbox" '. $attr['disabled'] .' value="" '. $attr['checked'] .' />'. $attr['text'] .'</label>
				</div>';

		return $html;
	}

	/**
	 * [PrepareTree description]
	 * @param array $list [description]
	 */
	static private function PrepareTree(array $list)
	{
		$html = '<ul>';

		foreach ($list as $item) {
			$html .= '<li id="struct_'. $item['id'] .'"><a href="" onclick="event.preventDefault()" id="_'. $item['id'] .'">'. $item['name'] .'</a>';
			if (count($item['children']) > 0) {
				$html .= self::PrepareTree($item['children']);
			}
			$html .= '</li>';
		}

		$html .= '</ul>';

		return $html;
	}

	/**
	 * Подготовка массива элементов выпадающего списка
	 * @param array массив с ключами/значенями
	 * @param array массив с ключами выбранных элементов
	 * @return string
	 */
	static private function PrepareList($attr)
	{
		extract($attr);

		$li = null;

		if (count($list)) {
			if (is_array(current($list))) {
				return static::PrepareGroupList($attr);
			}

			if ($showall) {
				if ($attr['showall'] === true)
					$attr['showall'] = "Все";

				$li .= '<li><a href="" data-list="-1" onclick="selectClear(event, this, \''. $id .'\')">'. $attr['showall'] .'</a></li>';
			}
			
			foreach ($list as $key => $value) {
				$activeClass = null;

				if (count($selected) && in_array($key, $selected)) {
					self::$placeholder = $value;
					$activeClass = 'selected';
				}

				$li .= '<li>
							<a data-list="'. $key .'" href="#" onclick="'. $onchange .'(event, this, \''. $id .'\', '. $callback .')" class="'. $activeClass .'"><span class="select-box"></span>'. $value .'</a>
						</li>';
			}
		}

		return $li;
	}

	/**
	 * [PrepareGroupList description]
	 * @param array  $list     [description]
	 * @param array  $selected [description]
	 * @param [type] $id       [description]
	 * @param [type] $onchange [description]
	 */
	static private function PrepareGroupList($attr)
	{
		extract($attr);

		$li = null;

		if ($showall) {
			$li .= '<li><a href="" data-list="-1" onclick="selectClear(event, this, \''. $id .'\')">Все</a></li>';
		}

		foreach ($list as $name => $groups) {
			$li .= '<li class="group" onclick="event.stopPropagation()">'. $name .'</li>';

			foreach ($groups as $key => $value) {
				$activeClass = null;

				if (count($selected) && in_array($key, $selected)) {
					self::$placeholder = $value;
					$activeClass = 'selected';
				}

				$li .= '<li>
							<a href="" data-list="'. $key .'" onclick="'. $onchange .'(event, this, \''. $id .'\', '. $callback .')" class="'. $activeClass .'"><span class="select-box"></span>'. $value .'</a>
						</li>';
			}
		}

		return $li;
	}

	/**
	 * Формирование HTML блока с выпадающим списком
	 * @param array массив опций
	 * @return string
	 */
	static private function DropDown(array $attr)
	{
		$searchBlock = null;
		$searchStack = self::uniqueId();

		$dropdownBlock = self::uniqueId();

		if ($attr['search'] === true) {
			$searchBlock = '<div class="dropdown-searchbox">
								<input class="form-control input-sm search-input" type="text" onkeyup="liveSearch(this, \''. $searchStack .' li\')" placeholder="поиск в списке">
							</div>';
		}

		$list = static::PrepareList($attr);

		$count = count($attr['selected']);
		if ($count == 1) {
			$placeholder = self::$placeholder;
		} elseif ($count > 1) {
			$placeholder = 'Выбрано '. $count;
		} else {
			$placeholder = $attr['placeholder'];
		}

		if ($attr['disabled']) {
			$attr['disabled'] = 'disabled="disabled"';
		}

		$html = '<div id="'. $attr['id'] .'" class="btn-group blue-base-group">
					<input name="'. $attr['name'] .'" type="hidden" value="'. implode(',', $attr['selected']) .'" />
					<button class="btn btn-default btn-xs" type="button" data-toggle="dropdown" data-dropdown="'. $dropdownBlock .'" '. $attr['disabled'] .' data-placeholder="'. $attr['placeholder'] .'">
						<span class="title">'. $placeholder .'</span>
						<span class="caret"></span>
					</button>
					<div id="dropdown-'. $dropdownBlock .'" class="dropdown-menu">
						'. $searchBlock .'
						<ul class="dropdown-ul '. $searchStack .'">
							'. $list .'
						</ul>
					</div>
				</div>';

		return $html;
	}

	public static function uniqueId()
	{
		$fix = microtime(true);
		$fix = str_replace('.', '', $fix) . rand(10000, 99999) . rand(10000, 99999);

		return uniqid($fix .'_');
	}
}
