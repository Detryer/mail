function Topic() {
	// var _Reference = this;

	this.write = function () {
		var modal;

		modal = new DialogModal({
			container: '#modal',
			title: 'Новое сообщение',
			dialogClass: 'material-ui',
			resizable: false,
			buttons: [
				{
					text: "Добавить",
					click: function () {
						var text = tinyMCE.activeEditor.getContent({format: 'raw'});

						if ($('[name="title"]').val().length == 0) {
							alert('Название темы не может быть пустым!');
							return false;
						}
						if (text.length == 0) {
							alert('Текст сообщения не может быть пустым!');
							return false;
						}
						addTopic(text);
						$(this).dialog("close");
					}
				},
				{
					text: "Отмена",
					click: function () {
						$(this).dialog("close");
					}
				}
			],
			width: "auto",
			height: "auto",
			modal: true,
			position: {my: "center", at: "center", of: window}
		});

		$.ajax({
			url: '/topic/addForm',
			type: 'POST',
			dataType: 'json',
			success: function (form) {
				modal.data(form);
				$(".ui-dialog-titlebar-close").hide();
				modal.open();
			}
		});

		function addTopic(text) {
			var formData = $("#topic_form").serializeArray();
			formData[formData.length] = {name: "text", value: text};
			$.ajax({
				url: '/topic/addTopic/',
				type: 'POST',
				dataType: 'json',
				data: {"addData": formData},
				success: function (data) {
				}
			});
		}
	};

	this.open = function (topicID, titleBlock) {
		if ($(titleBlock).prop('disabled')) {
			window.location.href = 'http://mail.dev/topic/' + topicID;
		} else {
			return false;
		}
	};

	this.users = function () {

	};

	this.editPost = function (postID, topicID) {
		var modal;
		modal = new DialogModal({
			container: '#modal',
			title: 'Редактирование сообщения',
			dialogClass: 'material-ui',
			resizable: false,
			buttons: [
				{
					text: "Сохранить",
					click: function () {
						var text = tinyMCE.activeEditor.getContent({format: 'raw'});

						if (text.length == 0) {
							alert('Текст сообщения не может быть пустым!');
							return false;
						}
						edit(text);
						$(this).dialog("close");
					}
				},
				{
					text: "Отмена",
					click: function () {
						$(this).dialog("close");
					}
				}
			],
			width: "auto",
			height: "auto",
			modal: true,
			position: {my: "center", at: "center", of: window}
		});

		$.ajax({
			url: '/topic/editPostForm/' + postID,
			type: 'POST',
			dataType: 'json',
			success: function (response) {
				modal.data(response);
				$(".ui-dialog-titlebar-close").hide();
				modal.open();
			}
		});

		function edit(text) {
			$.ajax({
				url: '/topic/editPost/',
				type: 'POST',
				dataType: 'json',
				data: {"text": text, "postID": postID, "topicID": topicID},
				success: function () {
					$('#post_' + postID).find('.text').html(text);
				}
			});
		}
	};

	this.move = function () {

	};

	this.trash = function () {

	};

	this.editTitle = function (topicID) {
		var titleBlock = $('#topic_' + topicID).find('.topic_title');
		titleBlock.prop('disabled', false);
		$('.title_editable').focus();
	};

	this.saveTitle = function (topicID, oldTitle) {
		var titleBlock = $('#topic_' + topicID).find('.topic_title');
		var newTitle = titleBlock.val();

		function saveNewTitle() {
			$.ajax({
				url: '/topic/editTitle/' + topicID,
				type: 'POST',
				data: {
					title: newTitle
				},
				success: function () {
					titleBlock.val(newTitle);
					modal.close();
				}
			});
		}

		var modal;
		modal = new ConfirmModal({
			width: 700,
			title: 'Переименование темы',
			description: 'Тема будет переименована. Продолжить?',
			buttons: [{
				id: 'save_title',
				text: 'Сохранить',
				class: 'primary-button',
				click: saveNewTitle
			}],
			open: function () {
				// titleBlock.val(oldTitle);
				// titleBlock.prop('disabled', true);
			}
		});

		modal.open();
	};

	this.delete = function (topicID) {
		function deleteTopic() {
			$.ajax({
				url: '/topic/deleteTopic/' + topicID,
				success: (function () {
					modal.close();
					$('#topic_' + topicID).remove();
				})
			});
		}

		var modal;
		modal = new ConfirmModal({
			width: 700,
			title: 'Удаление темы',
			description: 'Тема будет удалена. Продолжить?',
			buttons: [{
				id: 'delete_title',
				text: 'Удалить',
				class: 'primary-button',
				click: deleteTopic
			}],
			open: function () {
			}
		});

		modal.open();
	};
}

var topic = new Topic();