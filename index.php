<?php
	require_once( 'library/bootswatch.class.php' );
	
	$bsd = new BOOTSWATCH();
	
	if($bsd){
		echo 'Themes downloaded & updated successfully.';
	}
?>