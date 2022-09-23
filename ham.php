<!DOCTYPE html>
<html>
<?php include("head.php"); ?>
<body>
<?php include('menu.php'); ?>
<table>
<tr style='vertical-align:top'><td>
<?php 
$ran = rand(1,1000000);
echo "<img align='top' src='http://www.hamqsl.com/solarvhf.php?=$ran'>&nbsp;&nbsp;";
echo "<img src='http://www.sws.bom.gov.au/Images/HF%20Systems/Global%20HF/Ionospheric%20Map/West/fof2_maps.png?=$ran'>";
?>
</td></tr>
<tr><td>
<?php
echo "<img align='top' src='https://www.timeanddate.com/scripts/sunmap.php?=$ran'>";
?>
</td></tr>
<tr><td>
<h3>Band Conditions</h3>
<a href='http://www.bandconditions.com' target='_blank'>Band Conditions</a><br>
<h3>DX</h3>
<a href='https://qsomap.org/' target='_balnk'>QSO Map</a><br>
<a href='https://www.qrz.com/dxcluster' target='_blank'>QRZ.com DX Cluster</a><br>
<a href='http://www.dxsummit.fi' target='_blank'>DX Summit</a><br>
<a href='https://dxheat.com/dxc/' target='_blank'>DX Heat</a><br>
<a href='https://www.levinecentral.com/ham/grid_square.php' target='_blank'>Grid Square Locator</a><br>
<h3>CW</h3>
<a href='https://lcwo.net' target='_blank'>Learn CW Online (LCWO)</a><br/>
<a href='http://www.arrl.org/w1aw-operating-schedule' target='_blank'>W1AW opoeration schedule</a><br/>
<h3>Beacon</h3>
<a href='https://www.ncdxf.org/beacon/index.html' target='_blank'>NCDXF/IARU International Beacon Project</a><br/>
<a href='http://www.qsl.net/wj5o/bcn.htm' target='_blank'>10 METER BEACON LIST</a><br/>
<a href='https://www.keele.ac.uk/depts/por/28.htm', target='_blank'>G3USF's Worldwide List of HF Beacons</a><br/>
<h3>SSB</h3>
<a href='http://www.docksideradio.com/Cruising%20Nets.htm' target='_blank'>Dock Side radio</a><br/>
<a href='http://cruising.coastalboating.net/Seamanship/Radio/RadioFreq.html' target='_blank'>Top Marine SSB/HAM Radio Frequencies </a><br/>
<h3>HF</h3>
<a href='http://www.arrlntx.org/national-traffic-system/traffic-nets' target='_blank'>Texas and Southwestern HF Nets</a><br/>
<a href='http://www.rodscott.photography/n1yz-hf-new-net-list/' target='_blank'>N1YZ HF Net List</a><br/>
<a href='http://www.virhistory.com/ham/rrab/air.htm' target='_blank'>Boat anchors on the air</a><br/>
<h3>HAM Resources</h3>
<a href='https://www.scc-ares-races.org/generalinfo/phoneticalphabet.html' target='_blank'>Amateur Radio Phonetic Alphabet</a><br/>
<a href='https://www.heavens-above.com/AmateurSats.aspx' target='_blank'>Amateur Radio Satellites - Heavens Above</a>
<h3>QSO Parties</h3>
<a href='https://qsoparty.eqth.net/index.html' target='_blank'>QSO Party Calendar by N5NA</a><br/>
<a href='http://www.arrl.org/contest-calendar' target='_blank'>ARRL Contest Calendar</a>
</td></tr>
</table>
<?php include('tail.php'); ?>
</body>
</html> 
