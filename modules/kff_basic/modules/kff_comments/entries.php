<?php
self::$log->add(__METHOD__,null,['$this->check_no_comm()'=>$this->check_no_comm()]);
?>

<div id="wrapEntries">
	<h4 id="comments_header">Комментарии<?=" ( {$this->paginator['data_count']} )"?></h4>

	<?php //* Comments BLOCK
	if (
		self::is_adm()
		&& $this->check_no_comm()
	) echo self::T_DISABLED;

	if(self::is_adm()):
		// self::$log->add(__METHOD__,null,['self::$artDB->{\'enable-comments\'}'=>self::$artDB->{'enable-comments'}]);
	?>

	<div class="uk-text-center uk-margin">
		<label class="button uk-display-inline-block">
			<input class="uk-checkbox" onchange="commFns.en_com.call(this)" <?=!$this->check_no_comm() ?'checked="checked"':''?> type="checkbox"> Включить комментарии на этой странице
		</label>
	</div>

	<?php
	endif;

	$cpfr = count($this->paginator['fragm']);
	if($cpfr)
	{
		echo $this->paginator['html'];
		if ($cpfr > 3) echo '<p><a href="#comments_name" title="'. $this->Title .'">'. $this->Title .'</a></p>';
		echo "<div id=\"entries\">$comments</div>"
		. $this->paginator['html'];
	}
	else
	{
		echo self::T_EMPTY . '<p></p>';
	}

	?>


	<?php if(self::is_adm() && !empty($this->err)): ?>
		<div class="core warning">
			<pre>
				<?php
				echo "<h5>!!!</h5>";
				var_dump($this->err);
				?>
			</pre>
		</div>
	<?php endif ?>
</div>