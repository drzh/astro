<!DOCTYPE html>
<html>
<?php include 'head.php'; ?>
<body>
<?php include 'menu.php'; ?>
<?php
$ran = rand(1, 1000000);
?>
<section class="panel">
  <h2 class="panel-title">Propagation Maps</h2>
  <div class="media-grid-two">
    <figure class="media-panel media-panel--compact-graphic">
      <span class="media-panel__label">Solar VHF</span>
      <img class="media-panel__image--intrinsic" src="http://www.hamqsl.com/solarvhf.php?=<?php echo $ran; ?>" alt="Solar VHF conditions" loading="lazy" decoding="async">
    </figure>
    <figure class="media-panel media-panel--compact-graphic">
      <span class="media-panel__label">Ionospheric Map</span>
      <img class="media-panel__image--intrinsic" src="http://www.sws.bom.gov.au/Images/HF%20Systems/Global%20HF/Ionospheric%20Map/West/fof2_maps.png?=<?php echo $ran; ?>" alt="Ionospheric map" loading="lazy" decoding="async">
    </figure>
  </div>
</section>
<section class="panel">
  <h2 class="panel-title">Sun Map</h2>
  <figure class="media-panel">
    <img src="https://www.timeanddate.com/scripts/sunmap.php?=<?php echo $ran; ?>" alt="Global sun map" loading="lazy" decoding="async">
  </figure>
</section>
<section class="panel">
  <div class="resource-grid">
    <div class="resource-section">
      <h3>Band Conditions</h3>
      <div class="link-list">
        <a href='http://www.bandconditions.com' target='_blank' rel='noopener noreferrer'>Band Conditions</a>
      </div>
    </div>
    <div class="resource-section">
      <h3>DX</h3>
      <div class="link-list">
        <a href='https://qsomap.org/' target='_blank' rel='noopener noreferrer'>QSO Map</a>
        <a href='https://www.qrz.com/dxcluster' target='_blank' rel='noopener noreferrer'>QRZ.com DX Cluster</a>
        <a href='http://www.dxsummit.fi' target='_blank' rel='noopener noreferrer'>DX Summit</a>
        <a href='https://dxheat.com/dxc/' target='_blank' rel='noopener noreferrer'>DX Heat</a>
        <a href='https://www.levinecentral.com/ham/grid_square.php' target='_blank' rel='noopener noreferrer'>Grid Square Locator</a>
      </div>
    </div>
    <div class="resource-section">
      <h3>CW</h3>
      <div class="link-list">
        <a href='https://lcwo.net' target='_blank' rel='noopener noreferrer'>Learn CW Online (LCWO)</a>
        <a href='http://www.arrl.org/w1aw-operating-schedule' target='_blank' rel='noopener noreferrer'>W1AW operation schedule</a>
      </div>
    </div>
    <div class="resource-section">
      <h3>Beacon</h3>
      <div class="link-list">
        <a href='https://www.ncdxf.org/beacon/index.html' target='_blank' rel='noopener noreferrer'>NCDXF/IARU International Beacon Project</a>
        <a href='http://www.qsl.net/wj5o/bcn.htm' target='_blank' rel='noopener noreferrer'>10 METER BEACON LIST</a>
        <a href='https://www.keele.ac.uk/depts/por/28.htm' target='_blank' rel='noopener noreferrer'>G3USF's Worldwide List of HF Beacons</a>
      </div>
    </div>
    <div class="resource-section">
      <h3>SSB</h3>
      <div class="link-list">
        <a href='http://www.docksideradio.com/Cruising%20Nets.htm' target='_blank' rel='noopener noreferrer'>Dock Side radio</a>
        <a href='http://cruising.coastalboating.net/Seamanship/Radio/RadioFreq.html' target='_blank' rel='noopener noreferrer'>Top Marine SSB/HAM Radio Frequencies</a>
      </div>
    </div>
    <div class="resource-section">
      <h3>HF</h3>
      <div class="link-list">
        <a href='http://www.arrlntx.org/national-traffic-system/traffic-nets' target='_blank' rel='noopener noreferrer'>Texas and Southwestern HF Nets</a>
        <a href='http://www.rodscott.photography/n1yz-hf-new-net-list/' target='_blank' rel='noopener noreferrer'>N1YZ HF Net List</a>
        <a href='http://www.virhistory.com/ham/rrab/air.htm' target='_blank' rel='noopener noreferrer'>Boat anchors on the air</a>
      </div>
    </div>
    <div class="resource-section">
      <h3>HAM Resources</h3>
      <div class="link-list">
        <a href='https://www.scc-ares-races.org/generalinfo/phoneticalphabet.html' target='_blank' rel='noopener noreferrer'>Amateur Radio Phonetic Alphabet</a>
        <a href='https://www.heavens-above.com/AmateurSats.aspx' target='_blank' rel='noopener noreferrer'>Amateur Radio Satellites - Heavens Above</a>
        <a href='https://www.short-wave.info/' target='_blank' rel='noopener noreferrer'>Short-Wave Info</a>
      </div>
    </div>
    <div class="resource-section">
      <h3>QSO Parties</h3>
      <div class="link-list">
        <a href='https://qsoparty.eqth.net/index.html' target='_blank' rel='noopener noreferrer'>QSO Party Calendar by N5NA</a>
        <a href='http://www.arrl.org/contest-calendar' target='_blank' rel='noopener noreferrer'>ARRL Contest Calendar</a>
      </div>
    </div>
  </div>
</section>
<?php include 'tail.php'; ?>
</body>
</html>
