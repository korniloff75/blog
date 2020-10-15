<h4 id="comments_header">Комментарии<?=" ( {$this->paginator['data_count']} )"?></h4>

<?php
if (self::is_adm() &&
	($this->check_no_comm($this->p_name))
) echo self::T_DISABLED;
?>

<div id="wrapEntries">
	<?php # Comments BLOCK
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