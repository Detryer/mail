$(document).ready(function() {
	var $dropdownMenu;

	// Обработка открытия выпадающего списка
	$(window).on('show.bs.dropdown', function(e) {
		var buttonPosition = e.relatedTarget.getBoundingClientRect();

		$dropdownMenu = $(e.target).find('.dropdown-menu');

		$('body').append($dropdownMenu.detach());

		$dropdownMenu.css({
			'display': 'block',
			'min-width': 230,
			'max-width': 320,
			'width': $dropdownMenu.width(),
			'position': 'absolute'
		});

		var leftDir = buttonPosition.right - $dropdownMenu.width() - 2;
		if (leftDir < 15) {
			leftDir = buttonPosition.left;
		}

		$dropdownMenu.css({
			'top': buttonPosition.bottom + $(window).scrollTop(),
			'left': leftDir
		});

		setTimeout(function() {
			$dropdownMenu.find('.search-input').focus();
		}, 1);
	});

	// Обработка закрытия выпадающего списка
	$(window).on('hide.bs.dropdown', function(e) {
		$(e.target).append($dropdownMenu.detach());
		$dropdownMenu.hide();
	});

	// Прерываем закрытие окна по клику на окно поиска
	$('.dropdown-searchbox').on('click', function(event) {
		event.stopPropagation();
		return false;
	});

	window.user = new User({
		echo: "user"
	});
});

/**
 * 
 */
function selectSingle(event, elem, id, callback) {
	selectClear(event, elem, id);
	var $block = $('#' + id);
	var dataList = $(elem).data('list');
	var placeholder = $(elem).text();

	var dropdown = $block.find('button').data('dropdown');
	var $dropdown = $('#dropdown-'+ dropdown);

	$dropdown.find('[data-list="' + dataList + '"]').addClass('selected');
	$block.find('input[type="hidden"]').val(dataList);
	$block.find('button span.title').html(placeholder);

	if (typeof(callback) == "function") {
		callback(event, $(elem), dataList, true);
	}
}

/**
 * [selectMulti description]
 */
function selectMulti(event, elem, id, callback) {
	event.preventDefault();
	event.stopPropagation();

	var $block = $('#' + id);
	var dataList = $(elem).data('list');
	var values = [];

	var dropdown = $block.find('button').data('dropdown');
	var $dropdown = $('#dropdown-'+ dropdown);

	if ($(elem).hasClass('selected')) {
		$dropdown.find('[data-list="' + dataList + '"]').removeClass('selected');
		values = [];
	} else {
		$dropdown.find('[data-list="' + dataList + '"]').addClass('selected');
	}

	$dropdown.find('ul li').each(function() {
		if ($(this).children('a').hasClass('selected')) {
			values.push($(this).children('a').data('list'));
			lastSelected = $(this).children('a').text();
		}
	});

	values = $.unique(values);

	if (values.length > 1) {
		placeholder = 'Выбрано ' + values.length;
	} else if (values.length == 1) {
		placeholder = lastSelected;
	} else {
		placeholder = $block.find('button').data('placeholder');
	}

	$block.find('button span.title').html(placeholder);
	$block.find('input[type="hidden"]').val(values.join());

	if (typeof(callback) == "function") {
		var state = true;
		if (values.indexOf(dataList) == -1) {
			state = false;
		}
		callback(event, $(elem), dataList, state);
	}
}

/**
 * Сброс выделения (возрват на дефолтное значение)
 */
function selectClear(event, elem, id) {
	event.preventDefault();
	
	var $block = $('#' + id);
	var placeholder = $block.find('button').data('placeholder');

	var dropdown = $block.find('button').data('dropdown');
	var $dropdown = $('#dropdown-'+ dropdown);
	
	$dropdown.find('a').removeClass('selected');
	$block.find('button span.title').html(placeholder);
	$block.find('input[type="hidden"]').val(null);
	
	if (event.shiftKey) {
		event.stopPropagation();
		
		var thisEvent = event;
		
		$block.find('a').each(function(i) {
			if (i > 0) {
				selectMulti(thisEvent, $(this), id);
			}
		});
	}
}

/**
 * Поиск по элементам
 */
function liveSearch(elem, haystack) {
	var value = $.trim($(elem).val()).toLowerCase();
	
	$('.' + haystack).hide().each(function() {
		if ($(this).not('.group').text().toLowerCase().search(value) > -1) {
			$(this).prevAll('.group').first().add(this).show();
		}
	});
}

/**
 *
 */
function createDateRangePicker(blockId) {
	var $block = $('#' + blockId);
	var $defaultBlock = $block.children('input');
	var defaultValue = $defaultBlock.val().split('-');
	
	if ($block.length) {
		moment.locale('ru');
		
		$block.daterangepicker({
			startDate: moment.unix(defaultValue[0]).format('MM/DD/YYYY'),
//			endDate: moment.unix(defaultValue[1]).format('MM/DD/YYYY'),
//			maxDate: moment().add(24, 'hour'),
			showDropdowns: true,
			alwaysShowCalendars: true,
			locale: {
				applyLabel: 'Выбрать',
				cancelLabel: 'Закрыть',
				customRangeLabel: 'Свой вариант'
			},
			ranges: {
				'Сегодня': [moment(), moment()],
				'Вчера': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
				'Эта неделя': [moment().subtract(6, 'days'), moment()],
				'Прошлая неделя': [moment().subtract(1, 'weeks').startOf('week'), moment().subtract(1, 'weeks').endOf('week')],
				'Этот месяц': [moment().startOf('month'), moment().endOf('month')],
				'Прошлый месяц': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
				'Этот год': [moment().startOf('year'), moment().endOf('year')],
				'Прошлый год': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')],
			}
		}, function(start, end) {
			placeholderDatePicker(start, end, this.element);
		});
		
		$block.on('apply.daterangepicker', function(ev, picker) {
			var startDate, endDate;
			startDate = picker.startDate.unix();
			endDate = picker.endDate.unix();
			$(ev.target).find('input').val(startDate + '-' + endDate);
		});
		
		placeholderDatePicker(moment.unix(defaultValue[0]), moment.unix(defaultValue[1]), $block);
	}

	function placeholderDatePicker(start, end, $block) {
		$block.find('span').html(start.format('LL') + ' &ndash; ' + end.format('LL'));
	}
}

/**
 *
 */
function createDatePicker(blockId) {
	var $block = $('#' + blockId);
	var $defaultBlock = $block.children('input');
	var defaultValue = $defaultBlock.val();

	if ($block.length) {
		moment.locale('ru');

		$block.daterangepicker({
			startDate: moment.unix(defaultValue),
			singleDatePicker: true,
			showDropdowns: true,
			autoApply: true,
			timePicker: true,
			timePickerIncrement: 1,
			timePicker24Hour: true,
			template: '<div class="daterangepicker dropdown-menu">' +
							'<div class="calendar left">' +
								'<div class="calendar-table"></div>' +
								'<div class="daterangepicker_input">' +
								  '<input class="input-mini" type="text" name="daterangepicker_start" value="" />' +
								  '<div class="calendar-time">' +
									'<div></div>' +
								  '</div>' +
								'</div>' +
							'</div>' +
							'<div class="calendar right">' +
								'<div class="calendar-table"></div>' +
								'<div class="daterangepicker_input">' +
								  '<input class="input-mini" type="text" name="daterangepicker_end" value="" />' +
								  '<div class="calendar-time">' +
									'<div></div>' +
								  '</div>' +
								'</div>' +
							'</div>' +
						'</div>'
		}, function(date) {
			placeholderDatePicker(date, this.element);
		});

		$block.on('apply.daterangepicker hide.daterangepicker', function(ev, picker) {
			$(ev.target).find('input').val(picker.startDate.unix());
		});

		placeholderDatePicker(moment.unix(defaultValue), $block);
	}

	function placeholderDatePicker(date, $block) {
		$block.find('span').html(date.format('LLL'));
	}
}

/**
 *
 */
function createTimePicker(blockId) {
	var $block = $('#' + blockId);

	if ($block.length) {
		$block.clockpicker({
			autoclose: true
		});
	}
}

/**
 * 
 */
function createTreeList(groupId, blockId, type) {

	var $block = $('#' + blockId);
	var $dataTreeBlock = $('[data-treeblock="' + blockId + '"]');

	if ($block.length) {
		$dataTreeBlock.width($dataTreeBlock.width());
		$dataTreeBlock.on('click', function(event) {
			event.stopPropagation();
		})

		if (type == "single") {
			checkbox = null;
		} else {
			checkbox = "checkbox";
		}

		$block.on('changed.jstree', function(event, data) {
			changeTreeList(data, groupId);
		}).bind('loaded.jstree', function(e, data) {
			var values = $('#' + groupId).children('input').val();
			if (values.length > 0) {
				values = values.split(',');
				$.each(values, function(index, value) {
					$block.jstree('check_node', '#struct_' + value);

					if (type == "single")
						$block.jstree(true).select_node('#struct_' + value);
				});
			}
		}).jstree({
			"core": {
				"themes": {
					"icons": false
				}
			},
			"checkbox": {
				"keep_selected_style": false
			},
			"plugins": ["wholerow", checkbox]
		});
	}

	function changeTreeList(data, groupId) {
		var $group = $('#' + groupId);
		var values = [];
		
		for (var i in data.selected) {
			var item = data.selected[i].split('_');
			values.push(parseInt(item[1]));
		}

		if (data.selected.length > 0) {
			placeholder = 'Выбрано ' + data.selected.length;
/*		} else if (data.selected.length == 1) {
			var title = data.selected[0].split('_');
			placeholder = $('#_' + title[1]).text().trim();
*/		} else {
			placeholder = $group.find('button').data('placeholder');
		}
		
		$group.find('input[type="hidden"]').val(values.join());
		$group.find('button .title').html(placeholder);
	}
}

/**
 *
 */
function createTextArea(blockId) {
	var $blockId = $('#' + blockId);
	var $textArea = $blockId.children('textarea');
	var $counter = $blockId.children('span');
	
	$counter.html('0/' + $textArea.attr('maxlength'));
	
	$textArea.on('keyup', function(event) {
		var result = $textArea.attr('maxlength') - $textArea.val().length;
		$counter.html(result + '/' + $textArea.attr('maxlength'));
	});
}

/**
 * Класс модального окна
 * @param {object} params
 */
function Modal(params) {

	this.$dialogBox;

	this.dialogBoxPreloader = '<div class="preloader"><div class="spinner"></div><p>Получение данных</p></div>';

	this.defaultDialogOptions = {
		modal: true,
		autoOpen: false,
		position: {
			my: 'center center',
			at: 'center center',
			of: window
		},
		buttons: [{
			text: 'Закрыть',
			click: function() {
				this.dialogClose();
			}.bind(this)
		}]
	};

	/**
	 * Объявление модального окна
	 * @param {object} options
	 * @return {void}
	 */
	this.dialog = function(options)	{
		this.$dialogBox = $(options.container).dialog($.extend({}, this.defaultDialogOptions, options));

		return this;
	}

	/**
	 * Открытие модального окна
	 * @return {void}
	 */
	this.dialogOpen = function() {
		this.$dialogBox.children('.dialog-container').html(this.dialogBoxPreloader);
		this.$dialogBox.dialog('open');

		return this;
	}

	/**
	 * Добавление данных в модальное окно
	 * @param {string} html
	 * @return {void}
	 */
	this.dialogData = function(html) {
		this.$dialogBox.children('.dialog-container').html(html);

		return this;
	}

	/**
	 * Закрытие модального окна
	 * @return {void}
	 */
	this.dialogClose = function() {
		this.$dialogBox.dialog('close');
		this.$dialogBox.children('.dialog-container').html(null);

		return this;
	}
}

function User(params) {
	// Объявляем наследование
	Modal.apply(this, arguments);

	this.dialog({
		container: '#request-main-dialog',
		title: 'Модальное окно из класса User',
		width: 400,
		height: 270
	});
}

/**
 * Класс для управления модальными окнами
 * 
 * @param {Object} params свойства модального окна
 */
function DialogModal(params) {

	/**
	 * @type {Boolean} состояние загрузки данных в диалоговое окно
	 */
	var _filled = false;

	/**
	 * @type {this} ссылка на класс
	 */
	var _classReference = this;

	/**
	 * @type {String} прелоадер
	 */
	var _dialogBoxPreloader = '<div class="preloader"><div class="spinner"></div><p>Получение данных</p></div>';

	/**
	 * @type {Object} стандартные настройки jQueryDialog UI
	 */
	var _defaultDialogOptions = {
		modal: true,
		autoOpen: false,
		buttons: [{
			text: 'Закрыть',
			click: function() {
				_classReference.close();
			}
		}]
	};

	/**
	 * Конструктор
	 */

	var _$dialogBox = $(params.container).dialog($.extend({}, _defaultDialogOptions, params));

	/**
	 * Замена стандартного прелоадера
	 * 
	 * @param {String} html
	 * @return {this}
	 */
	this.preloader = function(html) {
		_dialogBoxPreloader = html;

		return this;
	}

	/**
	 * Вставка контента в диалоговое окно
	 * 
	 * @param {String} html 
	 * @return {this}
	 */
	this.data = function(html) {
		if (html) {
			_filled = true;
			_$dialogBox.children('.dialog-container').html(html);
		}

		return this;
	}

	/**
	 * Открытие диалогового окна
	 * 
	 * @return {this}
	 */
	this.open = function() {
		$('body').css('overflow', 'hidden');

		if (_filled === false) {
			_$dialogBox.children('.dialog-container').html(_dialogBoxPreloader);
		}

		_$dialogBox.dialog('open');

		return this;
	}

	/**
	 * Закрытие диалогового окна
	 * 
	 * @return {this}
	 */
	this.close = function() {
		$('body').css('overflow', 'inherit');

		_filled = false;
		_$dialogBox.dialog('close');
		_$dialogBox.children('.dialog-container').html(null);

		return this;
	}

}

function ConfirmModal(params) {

	var _classReference = this;

	var _closeButton = {
		text: 'Отмена',
		click: function() {
			_classReference.close();
		}
	};

	var _defaultDialogOptions = {
		modal: true,
		autoOpen: false,
		draggable: false,
		resizable: false,
		closeOnEscape: false,
		dialogClass: 'material-ui confirm-ui',
		buttons: [_closeButton]
	};

	if (params.buttons !== undefined) {
		params.buttons.push(_closeButton);
	}

	var _$dialogBox = $('#confirm-dialog').dialog($.extend({}, _defaultDialogOptions, params));

	if (params.description !== undefined) {
		_$dialogBox.children('.confirm-container').html(params.description);
	}

	this.open = function() {
		$('body').css('overflow', 'hidden');

		_$dialogBox.dialog('open');

		return this;
	}

	this.close = function() {
		$('body').css('overflow', 'inherit');

		_$dialogBox.dialog('close');
		_$dialogBox.children('.confirm-container').html(null);

		return this;
	}

}
