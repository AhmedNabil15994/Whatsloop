<?php 

class Helper{

	public static function RedirectWithPostForm(array $data,$url) {
        $fullData = $data;
        ?>
       	<html xmlns="http://www.w3.org/1999/xhtml">
           	<head>
               	<script type="text/javascript">
                   	function closethisasap() {
                       	document.forms["redirectpost"].submit();
                   	}
               	</script>
           	</head>
           	<body onload="closethisasap();">
               	<form name="redirectpost" method="post" action="<?PHP echo $url; ?>">
                   	<?php
                    if (!is_null($fullData)) {
                        foreach ($fullData as $k => $v) {
                            if(is_object($v) || is_array($v)){
                               	echo "<input type='hidden' name='".$k."' value='".json_encode((array)$v)."' >";
                            }else{
                               	echo "<input type='hidden' name='".$k."' value='".$v."' >";
                            }
                        }
                    }
                   ?>
               </form>
           	</body>
       	</html>
       	<?php
       	exit;
    }

}