<?php
echo date('D, Y-n-j, G:i T'), '&nbsp;[', gmdate('G:i'), ' UTC]'; ?>
<br/>

<a href='/index.php'>Site</a>

<?php if (file_exists('config/tgsky.off') || file_exists('../config/tgsky.off')) { ?>
  | <span class='del'>SkyCover</span>
<?php } else { ?>
  | <a href='/skycover.php'>SkyCover</a>
<?php } ?>

<?php if (file_exists('config/tgskyus.off') || file_exists('../config/tgskyus.off')) { ?>
  | <span class='del'>SkyCoverUS</span>
<?php } else { ?>
  | <a href='/skycoverus.php'>SkyCoverUS</a>
<?php } ?>

| <a href='/ndfd.php'>NDFD</a>
| <a href='/cloud.php'>Cloud</a>

<?php if (file_exists('config/tggoes.off') || file_exists('../config/tggoes.off')) { ?>
  | <span class='del'>GOES</span>
<?php } else { ?>
  | <a href='/goes.php'>GOES</a>
<?php } ?>

| <a href='/sfa.php'>SFA</a>
| <a href='/radar.php'>Radar</a>
<br/>

<?php if (file_exists('config/tgnam60.off') || file_exists('../config/tgnam60.off')) { ?>
<span class='del'>NAM-60</span>
<?php } else { ?>
<a class='alert' href='/nam60.php'>NAM-60</a>
<?php } ?>

<?php if (file_exists('config/tgnam84.off') || file_exists('../config/tgnam84.off')) { ?>
  | <span class='del'>NAM-84</span>
<?php } else { ?>
  | <a href='/nam84.php'>NAM-84</a>
<?php } ?>

| <a href='/nam84p.php'>NAM-84-P</a>
| <a href='/nam240p.php'>NAM-240-P</a>
| <a href='/nam384p.php'>NAM-384-P</a>
<br/>

<a href='/daynight.php'>Night</a>
| <a href='/light_pollution/lp.php'>LightPollution</a>
| <a href='/planets.php'>Planets</a>
| <a href='/table/table.php?tb=occultation.txt'>Occultation</a>
| <a href='/table/table.php?tb=cobs.commet.list.observed.json.txt'>Comets</a>
| <a href='/solar.php'>Solar</a>
<br/ >

<a href='/satellite_ha.php?sat=All&mag=3&max=20'>Sate (Vis)</a>
| <a href='/satellite.php?sat=ALL_PRI&max=20'>Sate (Ham)</a>
| <a href='/ham.php'>HAM</a>
| <a href='/table/table.php?tb=radnet.ft_worth.txt'>Radiation</a>
| <a href='/economy.php'>Eco</a>
| <a href='/link.php'>Links</a>
| <a href='/about.php'>About</a>
<br/>

<hr>

