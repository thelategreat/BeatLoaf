<?php
$on_button='selectcitytab';

$db_name='chicago';
$display_loc="Cities";
$display_fest="Festivals";
if ($_GET['loc']=='sf'){
$db_name='sanfran';
$display_loc="<b>San Francisco</b>";
}
if ($_GET['loc']=='chi'){
$db_name='chicago';
$display_loc="<b>Chicago</b>";
}
if ($_GET['loc']=='lv'){
$db_name='vegas';
$display_loc="<b>Las Vegas</b>";
}
if ($_GET['loc']=='la'){
$db_name='la';
$display_loc="<b>Los Angeles</b>";
}
if ($_GET['loc']=='abq'){
$db_name='abq';
$display_loc="<b>Albuquerque</b>";
}
if ($_GET['loc']=='nyc'){
$db_name='nyc';
$display_loc="<b>New York</b>";
}
if ($_GET['fest']=='lolla'){
$db_name='lolla';
$display_fest="<b>Lollapalooza</b>";
$on_button='selectfesttab';
}

date_default_timezone_set('America/Chicago');
?>
<!doctype html> 

<html>
<head>
<title>BeatLoaf - <?php echo $display_loc; ?></title>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-19480697-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</head>
<script type='text/javascript'>
pval=0;
function make_player(trackstring, artistname,displayvenue, displaydate,ticket_url,pval){

playliststring1= '<object width="220" height="400"><param name="movie" value="http://listen.grooveshark.com/widget.swf"><param name="wmode" value="window"><param name="allowScriptAccess" value="always"><param name="flashvars" value="hostname=cowbell.grooveshark.com&songIDs=';
playliststring2='&style=metal&bbg=2f6882&bfg=f5a720&bt=f0f5f5&bth=2f6882&pbg=f0f5f5&pbgh=f5a720&pfg=2f6882&pfgh=f0f5f5&si=f0f5f5&lbg=f0f5f5&lbgh=f5a720&lfg=2f6882&lfgh=f0f5f5&sb=f0f5f5&sbh=f5a720&p='+pval+'"><embed src="http://listen.grooveshark.com/widget.swf" type="application/x-shockwave-flash" width="220" height="400" flashvars="hostname=cowbell.grooveshark.com&songIDs=';
playliststring3='&style=metal&bbg=2f6882&bfg=f5a720&bt=f0f5f5&bth=2f6882&pbg=f0f5f5&pbgh=f5a720&pfg=2f6882&pfgh=f0f5f5&si=f0f5f5&lbg=f0f5f5&lbgh=f5a720&lfg=2f6882&lfgh=f0f5f5&sb=f0f5f5&sbh=f5a720&p='+pval+'" allowScriptAccess="always" wmode="window"></object>';

outString=playliststring1+trackstring+playliststring2+trackstring+playliststring3;

document.getElementById('playerwid').innerHTML=outString;
document.getElementById('playerhead').innerHTML="<div id='gsartist'><b>Playing: "+unescape(artistname)+"<b></div><div id='gsvenue'>"+unescape(displayvenue)+"</div><div id='gsdate'>"+displaydate+"</div>";

if (ticket_url != ''){
document.getElementById('playerhead').innerHTML = document.getElementById('playerhead').innerHTML + "<div id='gsticket'><a href='"+ticket_url+"?affil_code=BeatLoaf'>Buy Tickets</a></div>";
}
document.title = unescape(artistname)+": BeatLoaf - <?php echo $display_loc; ?>";

}

</script>
<link rel="stylesheet" type="text/css" href="test_style.css" />
<body>
    


<div id='container'>
<div id='header'>
<div id='banner'><div id='bannerimg'><img src='images/logo.gif'></div><div id='bannertext'><h1>BeatLoaf</h1></div></div>
</div>
<div id='headtabs'>
	<span id='selectcitytab'class='headtabitem' onmouseover='javascript:document.getElementById("selectcitydrop").style.display="block";'  onmouseout='javascript:document.getElementById("selectcitydrop").style.display="none";'>
		<span class='tablink'><span id='citytabhead'><?php echo $display_loc; ?></span></span>
		<span id='selectcitydrop' class='dropmenu'>
			<div class='dropitem' onclick='javascript:window.location="?loc=abq"'>Albuquerque</div>
			<div class='dropitem' onclick='javascript:window.location="?loc=chi"'>Chicago</div>
			<div class='dropitem' onclick='javascript:window.location="?loc=lv"'>Las Vegas</div>
			<div class='dropitem' onclick='javascript:window.location="?loc=la"'>Los Angeles</div>
			<div class='dropitem' onclick='javascript:window.location="?loc=nyc"'>New York</div>
			<div class='dropitem' onclick='javascript:window.location="?loc=sf"'>San Francisco</div>
		</span>
	</span>
	<span id='selectfesttab'class='headtabitem' onmouseover='javascript:document.getElementById("selectfestdrop").style.display="block";'  onmouseout='javascript:document.getElementById("selectfestdrop").style.display="none";'>
		<span class='tablink'><span id='citytabfest'><?php echo $display_fest; ?></span></span>
		<span id='selectfestdrop' class='dropmenu'>
			<div class='dropitem' onclick='javascript:window.location="?fest=lolla"'>Lollapalooza</div>
		</span>
	</span>
<div id='fbbutton'><fb:login-button autologoutlink="true"></fb:login-button><br><br></div>
</div>
<div id='content'><div id='leftcol'></div>
<?php
	$con = mysql_connect('127.0.0.1', 'root', 'cradle69');
	if (!$con)
	{
		die('Could not connect: ' . mysql_error());
	}
	echo '<div id="dateselect"><h3>Select Dates</h3>';
	$sql="SELECT DISTINCT date FROM `bnm`.`".$db_name."` where songs not like '[]' order by date;";//  where aid like '".$_GET['aid']."';";
	
	$dateresult = mysql_query($sql);
	$datei = 0;
	while ($row = mysql_fetch_array($dateresult) and ($datei<10)){
	$selDate=substr($row['date'],5);
	$datei++;
	if ((strpos($_GET['dates'],$row['date']) === false) and ($_GET['dates'] != '')){
	
	echo "<span class='dateoff' id='".$row['date']."' onclick='javascript:toggledate(\"".$row['date']."\",\"dateon\");'><span class='dateind'></span><span class='datetext'>";
	}
	else{
	echo "<span class='dateon' id='".$row['date']."' onclick='javascript:toggledate(\"".$row['date']."\",\"dateoff\");'><span class='dateind'></span><span class='datetext'>";
	}
	echo $selDate;
	echo "</span></span>\n";
	}
	echo "<span id='setdates'><a href='javascript:submitdates();'>Set Dates</a></span>";
	echo '</div>';
	



?>
<div id='bandlist'>
<div id='playalloption' class='event'></div>
<?php

$wstring='';
if ($_GET['dates'] != ''){
	$datestring=explode(",",$_GET['dates']);
	$datestring=implode("' OR date like '",$datestring);
	
	//echo $datestring;
	$wstring=" where date like '".$datestring."'";
	}
	//echo $wstring;


	mysql_select_db("bnm", $con);

	$sql="select * from ".$db_name.$wstring." order by date, name;";//  where aid like '".$_GET['aid']."';";
	//echo $sql;
	$result = mysql_query($sql);
	$songstring='';
	$lastsong='';
	$lastdate='';
	//mktime(0, 0, 0, 7, 1, 2000)
	while ($row = mysql_fetch_array($result)){
		$songs = json_decode($row['songs']);
		$datearray=explode("-",urldecode($row['date']));
		if ($lastdate != $row['date']){
		if (count($songs) > 0) {
		echo '<div class="datehead">';
		$lastdate=$row['date'];
		}
		else {
		echo '<div class = "dateheadhide" style="display:none;">';
		}
		
		echo date("l, F j",mktime(0, 0, 0, $datearray[1], $datearray[2], $datearray[0]));
		
		echo '</div>';
		
		}
		
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
			$artistname=urlencode($row['name']);
			$curvenue=urlencode($row['venue']);
			$curdate=$row['date'];
			}
			echo '\',\''.urlencode($row['name']).'\',\''.urlencode($row['venue']).'\',\''.$row['date'].'\',\''.$row['ticket_url'].'\',1)"><img src="images/playoff.gif" alt=">"/></a></span>';
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
		
		$timearray=explode(":",$row[time]);
		if ($timearray[0]>12){
			$timearray[0] = $timearray[0] - 12;
			$daysplit="pm";
		}
		else{
			$daysplit="am";
		}
		echo $datearray[1]."-".$datearray[2]; //." ".$timearray[0].":".$timearray[1].$daysplit;
		echo "</span><span class='gcal'>";
		
		echo '<a href="http://www.google.com/calendar/event?action=TEMPLATE&text='.$row['name'].' at '.$row['venue'].'&dates='.str_replace("-","",$row['date']).'/'.(str_replace("-","",$row['date'])+1).'&location='.$row['venue'].'&trp=false&sprop=wadd&sprop=name:BeatLoaf" target="_blank"><img src="images/GoogleCalendar_small.jpg" border=0 alt="Add to Gooogle Calendar"></a></span>';
		echo '<span class="ical"><a href="make_ics.php?date='.str_replace("-","",$row['date']).'&artist='.$row['name'].' at '.$row['venue'].'"><img src="/images/iCal-icon.png" alt="Add to iCal our Outlook"></a>';
		echo "</span><span class='tickets'>";
		if ($row['ticket_url'] != ''){
		echo "<a href='".$row['ticket_url']."?affil_code=BeatLoaf'><img src='images/ticket.png' alt='Buy Tickets'></a>";
		}
		echo "</span>";
		echo "</div>\n";
		//echo count($songs);
		
	
	}


	echo "</div>\n";
	

	echo "<div id='player'><div id='playerhead'>Loading</div><div id='playerwid'></div>    <p><fb:like></fb:like></p>

    <div id='fb-root'></div></div>";
	
	
	
	echo "<span id='playall'>";
	//echo '<b>All Band Sampler</b><br>';
	echo '<a href="javascript:make_player(\'';
	echo $songstring;
	echo '\',\'All Bands\',\'\',\'\',\'\',1)"><span class="play"><img src="images/playoff.gif"></span><span id="playhead">Play All Upcoming Bands</span></a>';
	echo '</span>';
mysql_close($con);	
?>


<div style='clear:both'></div>
</div>	

</div>
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
ticket_url='<?php echo urldecode($ticket_url); ?>';
make_player(unescape('<?php echo urldecode($songstring); ?>'),displayname, displayvenue, displaydate, ticket_url);


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
window.location = "?dates="+termstring+"&loc=<?php echo $_GET['loc'];?>";
}
    
      window.fbAsyncInit = function() {
        FB.init({appId: '181513225240524', status: true, cookie: true,
                 xfbml: true});
      };
      (function() {
        var e = document.createElement('script');
        e.type = 'text/javascript';
        e.src = document.location.protocol +
          '//connect.facebook.net/en_US/all.js';
        e.async = true;
        document.getElementById('fb-root').appendChild(e);
      }());
    
document.getElementById('<?php echo $on_button; ?>').style.backgroundImage='url(images/headtabon.jpg)';
document.getElementById('player').style.left=(document.getElementById("content").offsetLeft+765)+'px';
</script>
</html>