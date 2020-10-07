<?php
# Deprecated

// $m_dir = $pth['folder']['modules'] . 'comments/';
$m_path = Path::fromRootStat(__DIR__);
// var_dump($m_path);
?>

<section id="comments">
	<link rel="stylesheet" href="/<?=$m_path?>/style.css">
	<?php require_once('comments.php'); ?>

	<script type="text/javascript" src="/<?=$m_path?>/comments.js"></script>
</section>

