<?php
// note old
// $Comments = $Comments ?? new Comments;

/*== Сохранение, редактирование и удаление комментариев по AJAX-запросу ==*/

$s_method = $_REQUEST['s_method'] ?? null;

switch($s_method) {
	# Добавляем новый коммент
	case 'write': $Comments->write(); break;
	# Выводим форму редактирования коммента
	case 'edit_comm': $Comments->Edit_Comm(); break;
	# Сохраняем отредактированный коммент
	case 'save_edit':
		$Comments->Save_Edit();
		break;
	# Удаляем коммент
	case 'del': $Comments->Del_Comm(); break;
	# включение/отключение комментариев
	case 'enable_comm': $Comments->Enabled_Comm($_POST['enable_comm']); break;
	default :
	# Выводим существующие комменты
	if (isset($Comments) && (!$Comments->check_no_comm($Comments->p_name) || \ADMIN)) {
		// var_dump(\ADMIN);
		$Comments->read();
	}
}

