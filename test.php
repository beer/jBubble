<?
	require(dirname(__FILE__)."/../_libs/init.inc.php");
	if($_GET["uid"] != "" or $_GET["uid"] != NULL)
	    $uid = $_GET["uid"];
	if($_POST["uid"] != "" or $_POST["uid"] != NULL)
	    $uid = $_POST["uid"];

	// total rocords
	$totalRecord = totalHitsRecord($uid);

	$unickname = getCelebrityField($uid,"name");
	$uname = getUserField($uid,"user_name");

	$today = date("Y-m-d");
	$yesterday = mktime (0,0,0,date("m") ,date("d")-1,date("Y"));
	
	$StartValue = strftime("%Y-%m-%d",$yesterday);
	$EndValue = $today;

?>
<html>
<head>
<meta content="text/html; charset=UTF-8" http-equiv="Content-Type"/>
<script src="/_js/prototype.js" type="text/javascript"> </script>
<style type="text/css">@import url(../_js/jscalendar/calendar-win2k-1.css);</style>
<link rel="stylesheet" type="text/css" href="/css/main.css" media="screen" />
<script type="text/javascript" src="../_js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="../_js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="../_js/jscalendar/calendar-setup.js"></script>
<title><?= $uname ?>::PIXNET</title>
</head>
<body>
<?
	if ($_POST["timeStartText"]){
		$StartValue = $_POST["timeStartText"];
		$StartInt   = intval(substr(str_replace("-","",$StartValue),2));
	}
	if ($_POST["timeEndText"]){
		$EndValue = $_POST["timeEndText"];
		$EndInt   = intval(substr(str_replace("-","",$EndValue),2));
	}
	if(isset($_POST["details"]) && $_POST["details"] != NULL ){
	    $details = $_POST["details"];
	}
	if($_POST["timeEndText"] != NULL and $_POST["timeStartText"] != NULL){
	    if((strtotime($EndValue) - strtotime($StartValue)) < 0){
		$value = "<img src=\"/images/s_notice.png\"/> 不合理日期, 請檢查開始與結束時間!";	
	    }else{
		$value .=<<<EOT
		<table style="border: 1px dashed;"><tr><th>日期</th><th>人氣</th></tr>
EOT;
/*
		$hitArray = getHitArray($uid,$StartInt,$EndInt);
		foreach($hitArray as $k => $v){
		    echo $k."=>".$v;
		}*/
		$analysis = analysisHitsRecord($uid,$StartInt,$EndInt); //get avg,max,min
		$result = hitPeopleAnalysis($uid,$StartInt,$EndInt);
		// record size
		$resultSize = mysql_num_rows($result);
		while(list($date,$hits) = mysql_fetch_row($result)){
			$px = (int)180*$hits / 40000 ;
			$week = getWeek($date);
			$class = "length";
			if ($week == 6 or $week == 7)
			    $class = "holiday";
			$value .=<<<EOT
			<tr>
			  <td  class="$class" >$date</td>
			  <td>
			    <table class="length" cellpadding="0" cellspacing="0" style="width:180px;height:14px;margin-bottom:3px;">
			      <tr style="background-image:url(/images/storagebar.gif);">
				<td valign="top" style="width: $px;padding:0px;"><img src="/images/greenbar.gif" width="$px" height="14px"></td>
				<td>$hits</td>
			      </tr>
			    </table>
			  </td>
			</tr>
EOT;
		}
		$value .="</table>";
	    }	
	}else
	    $value = "";

	// for analysis chart
	//for(int $i=0 ;$i<300;$i++)


?>
<div class="selfinfo">
  <a href="http://blog.pixnet.net/<?=$uname;?>" target="_blank"><?= $unickname ?></a>&nbsp;&nbsp;&nbsp;&nbsp;(顯示比數:<?= $resultSize?>/<?= $totalRecord?>&nbsp;&nbsp;第一筆資料紀錄於:<font color="red"><?= getFirstHitsRecordDate($uid)?></font>&nbsp;&nbsp;AVG:<?=$analysis['avg']?>&nbsp;&nbsp;MAX:<?=$analysis['max']?>&nbsp;&nbsp;MIN:<?=$analysis['min']?>)
  <br/>
  <form id="selectTime" name="selectTime" action="/_themes/Total_reads_detail.inc.php?uid=<?=$uid?>" method="POST" onsubmit="return ckform();">
  <input type="text" name="timeStartText" id="timeStartText" size="8" value="<?= $StartValue?>"/> <input type=button id="strigger" value="...">~
  <input type="text" name="timeEndText" id="timeEndText" size="8" value="<?= $EndValue?>"/> <input type=button id="etrigger" value="...">
  <input name="details" type="checkbox" value="true"  <?if($details) echo "checked";?>/>細節
  <input type="submit" name="bsubmit" value="<?= _("查詢"); ?>"/>
  <input type="button" value="全部查詢" onClick="$('timeStartText').value='<?=getFirstHitsRecordDate($uid)?>'; $('timeEndText').value='<?=$today?>'; $('selectTime').submit();">
  </form>
</div>
<script language="JavaScript">
  document.write('<div id="nuvola"></div>')
  document.write('<img id="angolonuvola" src="/images/angolo.gif">')

  var offsetfromcursorX=12
  var offsetfromcursorY=10
  var offsetdivfrompointerX=10
  var offsetdivfrompointerY=14

  var ie=document.all
  var ns6=document.getElementById && !document.all
  var enabletip=false
  
  if(ie||ns6){
      var tipobj = $('nuvola')
      var pointerobj = $('angolonuvola')
  }

  function ietruebody(){
      return (document.compatMode && document.compatMode!="BackCompat")? document.documentElement : document.body
  }

  function ddrivetip(thetext, thewidth, thecolor){
      if (ns6||ie){
	  if (typeof thewidth!="undefined") tipobj.style.width=thewidth+"px"
	  if (typeof thecolor!="undefined" && thecolor!="") tipobj.style.backgroundColor=thecolor
	  tipobj.innerHTML= thetext;
	  enabletip=true
	  return false
      }
  }

  function positiontip(e){
      if(enabletip){
	  var nondefaultpos=false
	  var curX=(ns6)?e.pageX : event.clientX+ietruebody().scrollLeft;
	  var curY=(ns6)?e.pageY : event.clientY+ietruebody().scrollTop;

	  var winwidth=ie&&!window.opera? ietruebody().clientWidth : window.innerWidth-20
	  var winheight=ie&&!window.opera? ietruebody().clientHeight : window.innerHeight-20

	  var rightedge=ie&&!window.opera? winwidth-event.clientX-offsetfromcursorX : winwidth-e.clientX-offsetfromcursorX
	  var bottomedge=ie&&!window.opera? winheight-event.clientY-offsetfromcursorY : winheight-e.clientY-offsetfromcursorY

	  var leftedge=(offsetfromcursorX<0)? offsetfromcursorX*(-1) : -1000

	  tipobj.style.visibility="visible"
	  pointerobj.style.visibility="hidden"
	  //tipobj.innerHTML="X:"+curX +" ; Y"+curY+"; winw" + winwidth + " ; winh" + winheight + " ; rightedge" + rightedge + " ; bottomedge" + bottomedge+" ; leftedge"+leftedge +" ; offsetWidth" + tipobj.offsetWidth + " ; offsetHeight" +  tipobj.offsetHeight ;

	  if (rightedge<tipobj.offsetWidth){
	      tipobj.style.left=curX-tipobj.offsetWidth+"px"
	      nondefaultpos=true
	  }
	  else if (curX<leftedge)
	      tipobj.style.left="5px"
	  else{
	      tipobj.style.left=curX+offsetfromcursorX-offsetdivfrompointerX+"px"
	      pointerobj.style.left=curX+offsetfromcursorX+"px"
	  }

	  if (bottomedge<tipobj.offsetHeight){
	      tipobj.style.top=curY-tipobj.offsetHeight-offsetfromcursorY+"px"
	      nondefaultpos=true
	  }
	  else{
	      tipobj.style.top=curY+offsetfromcursorY+offsetdivfrompointerY+"px"
	      pointerobj.style.top=curY+offsetfromcursorY+"px"
	  }

	  tipobj.style.visibility="visible"

	  if (!nondefaultpos)
	      pointerobj.style.visibility="visible"
	  else
	      pointerobj.style.visibility="hidden"
      }
  }

  function hideddrivetip(){
      if (ns6||ie){
	  enabletip=false
	  tipobj.style.visibility="hidden"
	  pointerobj.style.visibility="hidden"
	  tipobj.style.left="-1000px"
	  tipobj.style.backgroundColor=''
	  tipobj.style.width=''
      }
  }

  document.onmousemove=positiontip
</script>
<div class="selfbox">
  <ul>
  <?if ($details =="true") {?>
  <li><div ><? echo $value ?></div></li>
  <? } ?>
  <li><div class="chart"><img src="../images/GD/images/line.php?uid=<?= $uid?>&st=<?=$StartInt?>&et=<?=$EndInt?>"/></div></li>
  <li><?// test tipNote && table px .by beer 20071001 ?>
      <div id="table_chart" class="chart"> <? echo $chart ?>
      <img usemap="#graf1" src="../images/blank.gif" background="../images/blank.gif"/>
      <map NAME="graf1" id="graf1">
	  <? 
	      function get_coords($x,$y){
		  $left = $x;
		  $top  = $y;
		  $right = $x + 5;
		  $bottom = $y - 5 ;
		  return "coords=\"{$left},{$top},{$right},{$bottom}\"";
	      }

	      $width = 400;
	      $height = 200;
	      $test = 5;
	      for($x = 1;$x <= $width; $x+=$test){
		  for($y = 1 ;$y <= $height; $y+=$test){
		      $chartX = $x;
		      $chartY = $height - $y;
	  ?>
	  <area class="area" nohref <?= get_coords($chartX,$chartY);?> onMouseover="ddrivetip('X:<?=$chartX?>,Y:<?=$y?>',70 ,'lightyellow')"; onMouseout="hideddrivetip()"/>
	  <?	  }
	      }
	  ?>
      </map>
      <div class="charttable">
      <table>
	  <?
		  for($y = 0 ;$y < $height; $y+=$test){
		  echo "<tr>";
		      for($x = 0;$x < $width; $x+=$test){
		      echo "<td width=\"$test\" height=\"$test\"></td>";
	  	  }
		  echo "</tr>";
	      }
	  ?>
      </tr>
      </table>
      </div>
      </div>
  </li>
  </ul>
</div>
</body>
</html>

<script language="JavaScript">
	//for select Calendar
	var initial_date=new Date();
        initial_date.setHours(0);
	initial_date.setMinutes(0);
	initial_date.setSeconds(0);
	initial_date.setMilliseconds(0);

	 Calendar.setup({
         inputField  :   "timeStartText",
         ifFormat        :   "%Y-%m-%d",
         showsTime   :   true,
         button    :   "strigger",
         date            : initial_date
         });
	 Calendar.setup({
         inputField  :   "timeEndText",
         ifFormat        :   "%Y-%m-%d",
         showsTime   :   true,
         button    :   "etrigger",
         date            : initial_date
         });
</script>
