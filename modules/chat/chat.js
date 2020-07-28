function isNumeric(n) {
	return !isNaN(parseFloat(n)) && isFinite(n);
	// http://learn.javascript.ru/number
}
var Chat = {
	run: function (options) {
		this.id = options.id;
		this.checkInterval = options.checkInterval;
		this.onlineCheckInterval = options.onlineCheckInterval;
		this.maxLength = options.maxLength;
		this.ticket = options.ticket;
		this.activ = true;
		this.count = 0;

		// Скрываем кнопку прокрутки 
		document.getElementById("down").style.display = "none";

		// Скрываем кнопку удаления сообщений
		document.getElementById("dellButton").style.display = "none";
		

		// Запрашиваем разрешение на уведомления в браузере
		if ("Notification" in window) {
			if (Notification.permission !== 'denied') {
				Notification.requestPermission(function (permission) {
					// Если пользователь разрешил, то начинаем отслеживать активность окна
					if (permission === "granted") {
						window.onfocus = function () {
							Chat.activ = true;
							console.log('onfocus');
						}
						window.onblur = function () {
							Chat.activ = false;
							console.log('onblur');

						}
					}
				});
			}
			
		}

		// Следим за размерами поля ввода
		var msgTextarea = document.getElementById('msg');
		msgTextarea.oninput = function () {
			Chat.resizeTextarea(msgTextarea);
		};

		// Загружаем звуки
		Chat.soundNew = new Audio();
		Chat.soundNew.src = '/modules/chat/new.wav';
		Chat.soundSend = new Audio();
		Chat.soundSend.src = '/modules/chat/send.wav';


		// Начинаем проверку на наличие новых сообщений
		this.newCheck();
		this.newCheckIntervalId = setInterval(function () {
			Chat.newCheck(); 
		}, this.checkInterval);

	},

	resizeTextarea: function (msgTextarea) {
		msgTextarea.style.height = 'auto';
		document.getElementById('form').classList.remove('resized');
		console.log('remove resized');
		if (msgTextarea.clientHeight < msgTextarea.scrollHeight) {
			msgTextarea.style.height = msgTextarea.scrollHeight + 'px';
			document.getElementById('form').classList.add('resized');
			console.log('add resized');
		} 

		if (msgTextarea.scrollHeight >= document.body.clientHeight/3) {
			msgTextarea.style.height = document.body.clientHeight/3 + 'px';
			console.log('document.body.clientHeight ' + document.body.clientHeight);
		}

		console.log('msgTextarea.clientHeight ' + msgTextarea.clientHeight);
		console.log('msgTextarea.scrollHeight ' + msgTextarea.scrollHeight);
		console.log('msgTextarea.style.height ' + msgTextarea.style.height);
		
	},


	openListOnline: function () {
		var loadlistonline = document.getElementById("loadlistonline");
		var listonline = document.getElementById("listonline");
		loadlistonline.innerHTML = 'Загрузка списка';
		JLoader.request(this.id + "/ajax/online", {
			responseToId: "loadlistonline",
			error: function (msg) {
				loadlistonline.innerHTML = msg;
			}
		});
		
		listonline.style.height = "500px";
	},
	closeListOnline: function () {
		var listonline = document.getElementById("listonline");
		listonline.style.height = 0;
	},
	

	dellCheck: function () {
		var checked = document.querySelectorAll("input[type=\"checkbox\"]:checked");
		var dellButton = document.getElementById("dellButton");
		if (checked.length > 0) {
			dellButton.innerHTML = "Удалить выделенное (" + checked.length + ")";
			dellButton.style.display = "";
		} else {
			dellButton.style.display = "none";
		}
	},


	dell: function () {
		var dellButton = document.getElementById("dellButton");
		dellButton.innerHTML = "Удаление...";

		var formData = new FormData();
		var checked = document.querySelectorAll("input[type=\"checkbox\"]:checked");
		for (var i = 0; i < checked.length; ++i) {

			formData.append(checked[i].name, checked[i].value);

			var commentDOM = document.getElementById("id" + checked[i].value);
			commentDOM.parentNode.removeChild(commentDOM);
		}
		formData.append("ticket", this.ticket);

		JLoader.request(this.id + "/ajax/dell", {
			data: formData,// Данные
			success: function (response) {
				if (response != "Error") {
					dellButton.innerHTML = "Готово";
				} else {
					dellButton.innerHTML = "Ошибка";
				}
				setTimeout(function () {
					// Скрываем кнопку удаления сообщений
					Chat.dellCheck();
				}, 3000);
			},
			error: function () {
				dellButton.innerHTML = "Ошибка";
			},
		});
		return false;
	},

	toUser: function (login) {
		var obj = document.getElementById("msg");
		obj.value += "[b]" + login + "[/b], ";
		obj.focus();
		obj.setSelectionRange(obj.value.length, obj.value.length);
	},

	clear: function () {
		if (document.getElementById("msg")) {
			document.getElementById("msg").value = "";
			document.getElementById("msg").style.height = 'auto';
			document.getElementById('form').classList.remove('resized');
		}
		this.report('');
	},

	clearChat: function () {
		if (document.getElementById("posts")) {
			document.getElementById("posts").innerHTML = "";
		}
	},

	report: function (msg) {
		if (document.getElementById("report")) {
			document.getElementById("report").innerHTML = msg;
		}
	},


	down: function () {
		document.getElementById("down").style.display = "none";
		this.scroll();
	},




	submit: function () {
		clearTimeout(this.newCheckIntervalId);
		var tmpmsg = msg.value;
		
		var ReportTimeoutId = setTimeout(function(){
			Chat.report('Отправка сообщения...');
		},200)

		this.scroll();

		var formData = new FormData(document.form);
		JLoader.request(this.id + "/ajax/add", {
			data: formData,// Данные
			success: function (response) {
				clearTimeout(ReportTimeoutId);
				Chat.clear();

				// Звук отправки сообщения
				Chat.soundSend.volume = 1;
				Chat.soundSend.play();
				
				if (tmpmsg.trim().toLowerCase() == 'clear') {
					Chat.load(0, Chat.scroll);
				} else {
					Chat.load(Chat.count, Chat.animatedScroll);
				}

				



				// console.log('response - '+response);
				// console.log('typeof - ' + typeof response);

				
				if (isNumeric(response)) {
					// 	Сообщение успешно добавлено
					Chat.count = response;
					
					
					
				} else {
					if (response == "Authorized") { Chat.report("Вы не авторизованы"); }
					else if (response == "Strlen") { Chat.report("Слишком длинное или короткое сообщение"); }
					else if (response == "Error") { Chat.report("Ошибка при добавлении сообщения"); }
					else if (response == "Ticket") { Chat.report("Ошибка при проверке безопасности"); }
					else { Chat.report("Неизвестные данные ответа сервера"); }
					msg.value = tmpmsg;
				}
				

				Chat.newCheckIntervalId = setInterval(function () {
					Chat.newCheck();
				}, Chat.checkInterval);
			},
			error: function (msg) {
				clearTimeout(ReportTimeoutId);
				Chat.report("Произошла ошибка при запросе к серверу");
				Chat.newCheckIntervalId = setInterval(function () {
					Chat.newCheck();
				}, Chat.checkInterval);
				msg.value = tmpmsg;
			}
		});
		return false;
	},

	
	load: function (count, scrollfunc) {
		if (count == 0) {
			this.clearChat();
		}
		JLoader.request(this.id + "/ajax/loadchat/" + count, {
			responseToIdAdd: "posts",
			success: function (response) {
				scrollfunc();
				console.log('load: ' + count);
			}
		});
	},

	scroll: function () {
		var target = document.body.scrollHeight - document.body.clientHeight;
		document.documentElement.scrollTop = target;
		document.body.scrollTop = target;
		console.log('scroll - ' + target);
	},

	animatedScroll: function () {
		var target = document.body.scrollHeight - document.body.clientHeight;
		var scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
		if ((target - scrollTop) < 400){
			var scrollIntervalId = setInterval(function () {
				
				var stp = 5;
			
				if ((document.documentElement.scrollTop + stp) >= target || (document.body.scrollTop + stp) >= target) {
					document.documentElement.scrollTop = target;
					document.body.scrollTop = target;
					clearInterval(scrollIntervalId);
				}

				document.documentElement.scrollTop += stp;
				document.body.scrollTop += stp;
			
				console.log('animatedScroll - ' + target);
			}, 15);
		} else {
			document.getElementById("down").style.display = "";
		}
		
	},


	notify: function () {
		
		if (!Chat.activ) {
			console.log('notify');
			// Проверка поддерживаемости браузером уведомлений
			if ("Notification" in window) {
				console.log(Notification.permission);
				if (Notification.permission == "granted" ) {
					
					// Если разрешено то создаем уведомлений
					var notification = new Notification("Чат", {
						tag: "chat",
						body: "В чате написано новое сообщение",
						icon: "/modules/chat/notification.png"
					});
					notification.onclick = function () {
						window.focus();
						this.close();
					};
				}
			}
		}
	},

	newCheck: function () {
			console.log('request newCheck');
			JLoader.request(Chat.id + "/ajax/newcheck", {
				success: function (response) {
					console.log("response newCheck: " + response);
					if (isNumeric(response)) {
						if (Chat.count < response) {
							//var newCount = response - сount;

							if (Chat.count == 0) {
								Chat.load(Chat.count, Chat.scroll);
								
							} else {
								
								Chat.load(Chat.count, Chat.animatedScroll);
								
								// Звук нового сообщения
								Chat.soundNew.volume = 0.8;
								Chat.soundNew.play();
							}

							// Браузерное уведомдение 
							Chat.notify();

						}

						if (Chat.count > response) {
							Chat.load(0, Chat.scroll);
						}

						Chat.count = response;
						console.log("Chat.count = " + Chat.count);
					} else {
						console.log("response newCheck: no Numeric");
					}
					
				}
			});
	}

}