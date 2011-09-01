<?php
	$con = mysql_connect('127.0.0.1', 'root', 'cradle69');
	if (!$con)
	{
		die('Could not connect: ' . mysql_error());
	}


	$sql="SELECT rating FROM `bnm`.`artists`  where  artists.idartists like '".rawurlencode($_GET['id'])."';";//  where aid like '".$_GET['aid']."';";
	//echo $sql;
	$result = mysql_query($sql);
	$row = mysql_fetch_array($result);
	if ($row['rating']==''){
		$rating=1;
	}
	else{
	$rating=$row['rating']+1;
	}
	
	$sql = "update bnm.artists set rating=".$rating." where artists.idartists like '".rawurlencode($_GET['id'])."';";
	echo $sql;
	$result = mysql_query($sql);
	$datei = 0;
	mysql_close($con);	
?>