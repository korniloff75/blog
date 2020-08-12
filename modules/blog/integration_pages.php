<?php
require(__DIR__.'/cfg.php');

if($blogConfig->navStyle == '0'){$page->headhtml.= '<style>
.noComments{display:block;max-width:100%;overflow:hidden;margin:10px 0;padding:10px !important;text-align:center;font-size:13px;font-family: Arial, sans-serif;border:2px solid #FFE25B;background:#FFF2B1;-moz-border-radius:4px;
-webkit-border-radius:4px;
-khtml-border-radius:4px;
border-radius:4px;color:#000;}
.msg{display:block;max-width:100%;overflow:hidden;margin:10px 0;padding:10px !important;text-align:center;color:#444;font-size:13px;font-family: Arial, sans-serif;border:2px solid #FFE25B;background:#FFF2B1;-moz-border-radius:4px;
-webkit-border-radius:4px;
-khtml-border-radius:4px;
border-radius:4px;
}
</style>';}

return null;
?>