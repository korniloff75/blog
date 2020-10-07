<h4 id="comments_header">Комментарии<?=" ( {$pager['data_count']} )"?></h4>

<?php
if (\ADMIN &&
	($this->check_no_comm($this->p_name))
) echo self::T_DISABLED;
?>

<div id="entries">
	<?php # Comments BLOCK
	$cpfr = count($pager['fragm']);
	if($cpfr)
	{
		echo $pager['paginator'];
		if ($cpfr > 3) echo '<p><a href="#comments_name" title="'. $this->Title .'">'. $this->Title .'</a></p>';
		echo $comments
		. $pager['paginator'];
	}
	else
	{
		echo self::T_EMPTY . '<p></p>';
	}

	?>


	<?php if(\ADMIN && !empty($this->err)): ?>
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