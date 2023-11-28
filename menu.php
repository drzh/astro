<?php
echo date('D, Y-n-j, G:i T'), '&nbsp;[', gmdate('G:i'), ' UTC]'; ?><br/>

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

<?php if (file_exists('config/tgnam60.off') || file_exists('../config/tgnam60.off')) { ?>
  | <span class='del'>NAM-60</span>
<?php } else { ?>
  | <a class='alert' href='/nam60.php'>NAM-60</a>
<?php } ?>

<?php if (file_exists('config/tgnam84.off') || file_exists('../config/tgnam84.off')) { ?>
  | <span class='del'>NAM-84</span>
<?php } else { ?>
  | <a href='/nam84.php'>NAM-84</a>
<?php } ?>

| <a href='/ndfd.php'>NDFD</a>
<br/ >

<a href='/cloud.php'>Cloud</a>

<?php if (file_exists('config/tggoes.off') || file_exists('../config/tggoes.off')) { ?>
  | <span class='del'>GOES</span>
<?php } else { ?>
  | <a href='/goes.php'>GOES</a>
<?php } ?>

| <a href='/sfa.php'>SFA</a>
| <a href='/radar.php'>Radar</a>
| <a href='/daynight.php'>Night</a>
| <a href='/planets.php'>Planets</a>
| <a href='/solar.php'>Solar</a>
<br/ >

<a href='/satellite_ha.php?sat=All&mag=3&max=20'>Sate (Vis)</a>
| <a href='/satellite.php?sat=ALL_PRI&max=20'>Sate (Ham)</a>
| <a href='/ham.php'>HAM</a>
| <a href='/radiation.php'>Radiation</a>
| <a href='/link.php'>Links</a>
| <a href='/about.php'>About</a>
<br/ >

2024 TSE: 
<a href='/eclipse2024/index.php?st=TX'>Site</a>
  | <a href='/eclipse2024/skycover.php'>SkyCover</a>
<?php if (file_exists('config/tgnam60.off') || file_exists('../config/tgnam60.off')) { ?>
  | <span class='del'>NAM-60</span>
<?php } else { ?>
  | <a class='alert' href='/eclipse2024/nam60.php'>NAM-60</a>
<?php } ?>
<?php if (file_exists('config/tgnam84.off') || file_exists('../config/tgnam84.off')) { ?>
  | <span class='del'>NAM-84</span>
<?php } else { ?>
  | <a href='/eclipse2024/nam84.php'>NAM-84</a>
<?php } ?>
  | <a href='/cloud.php?rg=TX&ch=GEOCOLOR&it=JPG&pa=tse2024'>Cloud</a>
<br/>

<hr>
