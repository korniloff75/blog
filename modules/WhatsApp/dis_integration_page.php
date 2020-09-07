<?php
if (!class_exists('System')) exit; // Запрет прямого доступа
require(__DIR__.'/info.dat');
ob_start();
?>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<link rel="stylesheet" href="../modules/WhatsApp/floating-wpp.min.css">
<script src="../modules/WhatsApp/floating-wpp.min.js"></script>

<div class="floating-wpp"></div>

<script>
	document.body.classList.add("loaded");
$(function () {
  $(".floating-wpp").floatingWhatsApp({

     // phone number
  phone: "<?=$WhatsApp['4']?>",

  // message to send
  message: "<?=$WhatsApp['3']?>",

  // or right left
  position: "left",

  // message in popup
  popupMessage: "<?=$WhatsApp['1']?>",

  // show a chat popup on hover false true
  showPopup: true,

  // in milliseconds
  autoOpenTimer: 1000,

  // header color
  headerColor: "#128C7E",

  // header title
  headerTitle: "<?=$WhatsApp['2']?>",

  // z-index property
  zIndex: 999

  });
});
</script>

<?php
return ob_get_clean();

?>
