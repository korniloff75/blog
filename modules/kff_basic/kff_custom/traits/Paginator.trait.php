<?php
/*
* @param data - массив элементов из файла
* Функция возвращает массив, содержащий фрагмент
* массива $data, соответствующий запросу $name_request
*/

trait Paginator
{

	public function Paginator (int $max_entries=10, string $name_request='p', $reverse=1, $hash="")

	{
		$paginator = '';

		$data= &$this->file;

		// var_dump($data);

		if(!$data_count = count($data)) return false;
		// var_dump($data_count);

		// if($reverse) $data= array_reverse($data);
		if($reverse) $data->reverse();

		$page_blocks_count=ceil($data_count/$max_entries);

		$p = $_REQUEST[$name_request] ?? "1";

		$first_page= ($p-1)*$max_entries;
		$last_page= $p*$max_entries; # -1


		if($page_blocks_count != 1) {
			$paginator .= "<div class=paginator data-id=\"$name_request\">";

			for($u=1; $u<=$page_blocks_count; $u++) {
				if($p!=$u){
					$paginator .= "<a href='/{$_REQUEST['page']}?$name_request= $u'>$u</a> ";
				}	elseif($p==$u){
					$paginator .= "<b>$u</b> ";
				}
			}

			$paginator .= "</div>";
		} else {
			$paginator = '';
		}

		return [
			'fragm'=>array_slice($data->get(),$first_page,$max_entries), #-1
			'paginator' => $paginator,
			'fp'=>$first_page,
			'lp'=>$last_page,
			'data_count'=>$data_count,
		];
	}
}