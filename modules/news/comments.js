var Comments = {
	run: function (options) {
		this.id = options.id;
		this.newCommentCheckInterval = options.newCommentCheckInterval;
		this.commentMaxLength = options.commentMaxLength;
		this.ticket = options.ticket;

		this.loginFormCheck();
		this.textFormCheck();
		this.loadComments(0);
	},

	commentDellCheck: function () {
		var checked = document.querySelectorAll("input[type=\"checkbox\"]:checked");
		var commentDellButton = document.getElementById("commentDellButton");
		if (checked.length > 0) {
			commentDellButton.innerHTML = "Удалить выделенное (" + checked.length + ")";
			commentDellButton.style.display = "";
		} else {
			commentDellButton.style.display = "none";
		}
	},


	commentDell: function () {
		var commentDellButton = document.getElementById("commentDellButton");
		commentDellButton.innerHTML = "Удаление...";

		var formData = new FormData();
		var checked = document.querySelectorAll("input[type=\"checkbox\"]:checked");
		for (var i = 0; i < checked.length; ++i) {

			formData.append(checked[i].name, checked[i].value);

			var commentDOM = document.getElementById("comment" + checked[i].value);
			commentDOM.parentNode.removeChild(commentDOM);
		} 
		formData.append("ticket", this.ticket);

		JLoader.request(this.id + "/ajax/dellcomments", {
			data: formData,// Данные
			success: function (response) {
				if (response != "Error") {
					commentDellButton.innerHTML = "Готово";
				} else {
					commentDellButton.innerHTML = "Ошибка";
				}
				setTimeout(function () {
					Comments.commentDellCheck();
				}, 3000);
			},
			error: function () {
				commentDellButton.innerHTML = "Ошибка";
			},
		});
		return false;
	},

	toUser: function (login) {
		window.location = '#commentForm';
		var obj = document.getElementById("textForm");
		obj.value += "[b]" + login + "[/b], ";
		obj.focus();
		obj.setSelectionRange(obj.value.length, obj.value.length);
	},

	clearCommentForm: function () {
		if (document.getElementById("loginForm")) {
			document.getElementById("loginForm").value = "";
		}
		if (document.getElementById("textForm")) {
			document.getElementById("textForm").value = "";
		}
		if (document.getElementById("captchaForm")) {
			document.getElementById("captchaForm").value = "";
			document.getElementById("captcha").src = "/modules/captcha/captcha.php?" + Math.random();
		}
	},

	loginFormCheck: function () {
		if (document.getElementById("loginForm")) {
			var loginForm = document.getElementById("loginForm");
			var loginReport = document.getElementById("loginReport");
			var loginFormCheck = false;
			loginForm.oninput = function () {
				if (loginFormCheck) {
					clearTimeout(loginFormCheck);
				}
				var loginFormTimer = setTimeout(function () {
					if (loginForm.value != "") {
						if (loginForm.value.length < 36) {
							var formData = new FormData();
							formData.append("login", loginForm.value);
							JLoader.request(Comments.id + "/ajax/validlogin", {
								data: formData,
								responseToId: "loginReport"
							});
						} else {
							loginReport.innerHTML = "Превышен лимит символов";
						}
					} else {
						loginReport.innerHTML = "";
					}
				}, 2000);
				loginFormCheck = loginFormTimer;
			}
		}
	},

	textFormCheck: function () {
		if (document.getElementById("textForm")) {
			var textForm = document.getElementById("textForm");
			var textReport = document.getElementById("textReport");
			textForm.oninput = function () {
				if (textForm.value.length > Comments.commentMaxLength) {
					textReport.innerHTML = "Превышен лимит символов";
				} else {
					textReport.innerHTML = "";
				}
			}
		}

	},


	requestReport: function (msg, color, timer) {
		if (document.getElementById("requestReport")) {
			var element = document.getElementById("requestReport");
			element.innerHTML = msg;
			element.style.display = "";
			element.className = color + " animatShow";
			if (timer) {
				setTimeout(function () {
					element.className += " animatHide";
				}, timer);
			}
		}
	},

	requestReportHide: function () {
		document.getElementById("requestReport").style.display = "none";
	},

	submitCommentForm: function () {
		this.requestReport("Отправка...", "grey");
		if (document.getElementById("commentDellButton")) {
			document.getElementById("commentDellButton").style.display = "none";
		}

		var formData = new FormData(document.commentForm);
		JLoader.request(this.id + "/ajax/addcomment", {
			data: formData,// Данные
			success: function (response) {
				Comments.clearCommentForm();

				//console.log(response);

				if (!isNaN(response)) {
					Comments.newCommentCheckApply(response);
					Comments.requestReport("Сообщение успешно добавлено", "green");
				} else {
					if (response == "Moderation") { Comments.requestReport("Сообщение отправлено на модерацию", "green"); }
					if (response == "Captcha") { Comments.requestReport("Символы на картинке не совпадают", "red"); }
					if (response == "Exists") { Comments.requestReport("Логин уже существует", "red"); }
					if (response == "Error") { Comments.requestReport("Ошибка при добавлении сообщения", "red"); }
					if (response == "Ticket") { Comments.requestReport("Ошибка при проверке безопасности", "red"); }
					if (response == "Ban") { Comments.requestReport("Сообщения с вашего ip заблокированы", "red"); }
				}

				setTimeout(function () {
					Comments.loadComments(0);
				}, 1000);
			},
			error: function (msg) {
				Comments.requestReport("Произошла ошибка при запросе к серверу", "red");
			},
		});
		return false;
	},

	loadComments: function (position) {
		if (document.getElementById("loadCommentsButton")) {
			var loadCommentsButton = document.getElementById("loadCommentsButton");
			loadCommentsButton.parentNode.removeChild(loadCommentsButton);
		}

		if (document.getElementById("commentDellButton")) {
			document.getElementById("commentDellButton").style.display = "none";
		}

		var options = {}
		if (position == 0) {
			options.responseToId = "comments";
		} else {
			options.responseToIdAdd = "comments";
		}
		options.error = function (msg) {
			Comments.requestReport("Произошла ошибка при запросе к серверу", 'grey');
		}
		options.success = function (msg) {
			Comments.newCommentCheck(0);
		}

		JLoader.request(this.id + "/ajax/loadcomments/" + position, options);

	},

	newCommentCheck: function (commentCount) {
		if (this.newCommentCheckInterval != 0) {
			//console.log("---newCommentCheck");
			JLoader.request(this.id + "/ajax/newcommentcheck", {
				success: function (response) {
					//console.log("response: " + response);

					if (commentCount != 0) {
						if (commentCount < response) {
							var newCount = response - commentCount;
							Comments.requestReport("Есть новые сообщения (" + newCount + ") <a href=\"javascript:void(0);\"  onClick=\"Comments.newCommentShow();\">Показать</a>", "grey");
						}
						if (commentCount > response) {
							Comments.newCommentCheckApply(response);
						}
					} else {
						Comments.newCommentCheckApply(response);
					}

				}
			});
		}
		//console.log("---newCommentCheck");


	},

	newCommentCheckApply: function (commentCount) {
		if (this.newCommentCheckInterval != 0) {
			if (this.newCommentCheckIntervalId) {
				clearInterval(this.newCommentCheckIntervalId);
			}
			this.newCommentCheckIntervalId = setInterval(function () {
				Comments.newCommentCheck(commentCount);
			}, this.newCommentCheckInterval);
		}	
	},

	newCommentShow: function () {
		this.requestReportHide();
		this.loadComments(0);
	}








}