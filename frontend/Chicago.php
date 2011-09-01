<html>
<head>
<title>BeatLoaf</title>

</head>
<script type='text/javascript'>
function make_player(trackstring, artistname,displayvenue, displaydate){

playliststring1= '<object width="220" height="400"><param name="movie" value="http://listen.grooveshark.com/widget.swf"><param name="wmode" value="window"><param name="allowScriptAccess" value="always"><param name="flashvars" value="hostname=cowbell.grooveshark.com&songIDs=';
playliststring2='&style=metal&bbg=2f6882&bfg=f5a720&bt=f0f5f5&bth=2f6882&pbg=f0f5f5&pbgh=f5a720&pfg=2f6882&pfgh=f0f5f5&si=f0f5f5&lbg=f0f5f5&lbgh=f5a720&lfg=2f6882&lfgh=f0f5f5&sb=f0f5f5&sbh=f5a720&p=0"><embed src="http://listen.grooveshark.com/widget.swf" type="application/x-shockwave-flash" width="220" height="400" flashvars="hostname=cowbell.grooveshark.com&songIDs=';
playliststring3='&style=metal&bbg=2f6882&bfg=f5a720&bt=f0f5f5&bth=2f6882&pbg=f0f5f5&pbgh=f5a720&pfg=2f6882&pfgh=f0f5f5&si=f0f5f5&lbg=f0f5f5&lbgh=f5a720&lfg=2f6882&lfgh=f0f5f5&sb=f0f5f5&sbh=f5a720&p=0" allowScriptAccess="always" wmode="window"></object>';

outString=playliststring1+trackstring+playliststring2+trackstring+playliststring3;

document.getElementById('playerwid').innerHTML=outString;
document.getElementById('playerhead').innerHTML="<div id='gsartist'><b>Playing: "+artistname+"<b></div><div id='gsvenue'>"+displayvenue+"</div><div id='gsdate'>"+displaydate+"</div>";
document.title = 'BeatLoaf - Playing: '+artistname;

}

</script>
<link rel="stylesheet" type="text/css" href="style.css" />
<body>
<div id='headerspace'></div><div style='clear:both'></div>
<div id='player'><div id='playerhead'>Loading</div><div id='playerwid'></div></div>
<div id='bandlist'>
<?php

$wstring='';
if ($_GET['dates'] != ''){
	$datestring=explode(",",$_GET['dates']);
	$datestring=implode("' OR date like '",$datestring);
	
	//echo $datestring;
	$wstring=" where date like '".$datestring."'";
	}
	//echo $wstring;
$con = mysql_connect('127.0.0.1', 'root', 'cradle69');
	if (!$con)
	{
		die('Could not connect: ' . mysql_error());
	}

	mysql_select_db("bnm", $con);

	$sql="select * from Chicago".$wstring.";";//  where aid like '".$_GET['aid']."';";
	//echo $sql;
	$result = mysql_query($sql);
	$songstring='';
	$lastsong='';
	while ($row = mysql_fetch_array($result)){
		$songs = json_decode($row['songs']);
		if (count($songs) > 0){
			echo "<div class='event'>";
			if (strrpos($songstring,$songs[0]) == false){
				if (($songstring != '')) {
					$songstring.=','.$songs[0];
				
					//echo "---------".$songs[0];
				}
				else{
					$songstring.=$songs[0];
				}
			$lastsong = $songs[0];	
			}
			echo '<span class="play"><a class ="playerlink" href="javascript:make_player(\'';
			echo implode(",", $songs);
			if (urldecode(trim(strtolower($_GET['a']))) == urldecode(trim(strtolower($row['name'])))){
			$artiststring=implode(",", $songs);
			$artistname=$row['name'];
			$curvenue=$row['venue'];
			$curdate=$row['date'];
			}
			echo '\',\''.$row['name'].'\',\''.$row['venue'].'\',\''.$row['date'].'\')"><img src="images/playoff.gif" alt=">"/></a></span>';
			echo "\n<!--".$songs[0]."---".$lastsong."---".$songstring."-->";
			//echo (urldecode(trim(strtolower($_GET['a']))) == urldecode(trim(strtolower($row['name']))));
		}
		else{
		echo "<div class='event' style='display:none'>";
		echo '<span class="noplay"></span>';
		}
		
		echo '<span class="artist"><b>'.urldecode($row['name']).'</b></span>';
		echo '<span class="venue">';
		echo urldecode($row['venue']);
		echo "</span>";
		echo '<span class="datetime">';
		$datearray=explode("-",urldecode($row['date']));
		echo $datearray[1]."-".$datearray[2];
		echo "</span><span class='gcal'>";
		
		echo '<a href="http://www.google.com/calendar/event?action=TEMPLATE&text='.$row['name'].' at '.$row['venue'].'&dates='.str_replace("-","",$row['date']).'/'.(str_replace("-","",$row['date'])+1).'&location='.$row['venue'].'&trp=false&sprop=wadd&sprop=name:BeatLoaf" target="_blank"><img src="images/GoogleCalendar_small.jpg" border=0></a>';
		//echo '<a href="http://www.google.com/calendar/event?action=TEMPLATE&text='.$row['name'].' at '.$row['venue'].'&dates='.implode("",explode("-",$row['date'])).'Z&location='.$row['venue'].'&trp=false&sprop;=website:http://www.BeatLoaf.com&sprop=name:BeatLoaf" target="_blank"><img src="images/GoogleCalendar_small.jpg" alt="Add to Google Calendar" ></a>';
		echo "</span></div>\n";
		//echo count($songs);
		
	
	}


	echo "</div>\n<div id='header'>";
	echo "<div id='banner'><h1>BeatLoaf</h1></div>";
	echo "<div id='playall'>";
	//echo '<b>All Band Sampler</b><br>';
	echo '<a href="javascript:make_player(\'';
	echo $songstring;
	echo '\',\'All Bands\',\'\',\'\')"><img src="images/playoffbig.gif"><span id="playhead">Play All Upcoming Bands</span></a>';
	echo '</div> <br><div id="dateselect">';
	$sql="SELECT DISTINCT date FROM `bnm`.`chicago` order by date;";//  where aid like '".$_GET['aid']."';";
	
	$dateresult = mysql_query($sql);
	while ($row = mysql_fetch_array($dateresult)){
	$selDate=substr($row['date'],5);
	
	if ((!strpos($_GET['dates'],$row['date'])) and ($_GET['dates'] != '')){
	
	echo "<span class='dateoff' id='".$row['date']."' onclick='javascript:toggledate(\"".$row['date']."\",\"dateon\");'><span class='dateind'></span><span class='datetext'>";
	}
	else{
	echo "<span class='dateon' id='".$row['date']."' onclick='javascript:toggledate(\"".$row['date']."\",\"dateoff\");'><span class='dateind'></span><span class='datetext'>";
	}
	echo $selDate;
	echo "</span></span>\n";
	}
	echo "<a href='javascript:submitdates();'>Set Dates</a>";
	echo '</div></div>';
mysql_close($con);	
?>	
	
	

</body>
<script type='text/javascript'>
<?php 
$displayName = "All Bands";
if ($artiststring != ''){
$songstring = $artiststring;
$displayName = $artistname;
}
?>
displayname='<?php echo urldecode($displayName); ?>';
displayvenue='<?php echo urldecode($curvenue); ?>';
displaydate='<?php echo urldecode($curdate); ?>';

make_player(unescape('<?php echo urldecode($songstring); ?>'),displayname, displayvenue, displaydate);


function toggledate(did,dclass){
	document.getElementById(did).setAttribute("class",dclass);
	if (dclass=="dateoff"){
		document.getElementById(did).setAttribute("onclick",'javascript:toggledate("'+did+'","dateon")');
	}
	else{
		document.getElementById(did).setAttribute("onclick",'javascript:toggledate("'+did+'","dateoff")');
	}
	
}

function submitdates(){
termstring=""
onarray=document.getElementsByTagName("span");
for (i=0;i<onarray.length;i++){
	if (onarray[i].className=='dateon'){
	if (termstring==""){
		termstring=onarray[i].id;
	}
	else{
	termstring = termstring+","+onarray[i].id;
	}
	}
}
window.location = "?dates="+termstring;
}

</script>
</html>