#!/usr/bin/perl

# usage: <STDIN> | prog | <STDOUT>

# Based on the javascript : https://skyandtelescope.org/observing/interactive-sky-watching-tools/transit-times-of-jupiters-great-red-spot/
# Longitude of Great Red Spot : http://jupos.privat.t-online.de/rGrs.htm
#
# Updated on 7/25/2022:
#     my $Red_Spot_init = 22.0;
#     my $Red_Spot = $Red_Spot_init + $m * 2.5;
#     my $start_yy = 2021;
#     my $start_mm = 9;


use strict;
use warnings;
use POSIX;
use Math::Trig;

sub get_red_spot_pos {
  my ($yy, $mm) = @_;
  my $Red_Spot_init = 78.0;
  my $start_yy = 2025;
  my $start_mm = 10;
  my $m = ($yy - $start_yy) * 12 + $mm - $start_mm;
  my $Red_Spot = $Red_Spot_init + $m * 2.5;
  return $Red_Spot
}

my $zone = 0;

while (<>) {
  chomp;
  jgrs($_);
}

sub jgrs {
  my $dt = $_[0];
  $dt =~ /(\d\d\d\d)(\d\d)(\d\d)/;
  my $yy = $1;
  my $mm = $2;
  my $dd = $3;

  my $Red_Spot = get_red_spot_pos($yy, $mm);

  my $yyy = $yy;
  my $mmm = $mm;

  if ($mm < 3) {
    $yyy = $yy - 1;
    $mmm = $mm + 12;
  }
  my $day = $dd + 0 / 1440;
  my $a = floor($yy / 100);
  my $b = 2 - $a + floor($a / 4);

  my $jd = floor(365.25 * $yyy) + floor(30.6001 * ($mmm + 1)) + $day + 1720994.5 + $b;

  my $RAD = 57.29578;

  my $days = $jd - 2415020.0;
  my $V = proper_ang(134.63 + 0.00111587 * $days);
  $V = 0.33 * sin($V / $RAD);
  my $M = proper_ang(358.476 + 0.9856003 * $days) / $RAD;
  my $N = proper_ang(225.328 + 0.0830853 * $days + $V) / $RAD;
  my $J = proper_ang(221.647 + 0.9025179 * $days - $V);
  my $A = 1.916 * sin($M) + 0.02 * sin(2 * $M);
  my $B = 5.552 * sin($N) + 0.167 * sin(2 * $N);
  my $K = ($J + $A - $B) / $RAD;
  my $R = 1.00014 - 0.01672 * cos($M) - 0.00014 * cos(2 * $M);
  my $r = 5.20867 - 0.25192 * cos($N) - 0.0061 * cos(2 * $N);
  my $e_to_j = sqrt(pow($r, 2) + pow($R, 2) - 2 * $r * $R * cos($K));
  my $pha = asin($R / $e_to_j * sin($K)) * $RAD;

  my $System_II = proper_ang(290.28 + 870.1869088 * ($days - $e_to_j / 173) + $pha - $B + 0.6);

  my ($trans, $indx, $i, $t_zone, $jd_temp, $ampm);
  my $result;
  my @rs_time;

  my $diff = proper_ang($Red_Spot - $System_II);

  for ($i = 0; $i < 3; $i++) {
    my $result = "";
    if ($i < 3) {
      $indx = $i;
      $t_zone = 0;
    }
    else {
      $indx = $i - 3;
      $t_zone = $zone;
    }

    $trans = ($diff + 360.0 * $indx) / 870.1869088 * 24.0;
    $jd_temp = $jd + $trans / 24.0 - $t_zone + 0.5;

    my $zz = floor($jd_temp);
    my $ff = $jd_temp - $zz;
    my $alpha = floor(($zz - 1867216.25) / 36524.25);
    my $aa = $zz + 1 + $alpha - floor($alpha / 4);
    my $bb = $aa + 1524;
    my $cc = floor(($bb - 122.1) / 365.25);
    my $dd = floor(365.25 * $cc);
    my $ee = floor(($bb - $dd) / 30.6001);
    my $calendar_day = $bb - $dd - floor(30.6001 * $ee) + $ff;
    my $calendar_month = $ee;

    if ($ee < 13.5) {
      $calendar_month = $ee - 1;
    }
    if ($ee > 13.5) {
      $calendar_month = $ee - 13;
    }
    my $calendar_year = $cc;

    if ($calendar_month > 2.5) {
      $calendar_year = $cc - 4716;
    }

    if ($calendar_month < 2.5) {
      $calendar_year = $cc - 4715;
    }

    my $int_day = floor($calendar_day);
    my $hours = ($calendar_day - $int_day) * 24;
    my $minutes = floor(($hours - floor($hours)) * 60 + 0.5);

    if ($minutes > 59) {
      $minutes = 0;
      $hours = $hours + 1;
    }

    if ($calendar_month < 10) {
      $result = $result . "0" . $calendar_month;
    }
    else {
      $result = $result . $calendar_month;
    }
    # $result = $result . "/";

    if ($int_day < 10) {
      $result = $result . "0" . $int_day;
    }
    else {
      $result = $result . $int_day;
    }
    $result = $calendar_year . $result . "\t";

    # if ($i < 3) {
    if ($hours < 10) {
      $result = $result . "0" . floor($hours);
    }
    else {
      $result = $result . floor($hours);
    }
    # $result = $result . ":";
    if ($minutes < 10) {
      $result = $result . "0" . $minutes;
    }
    else {
      $result = $result . $minutes;
    }
    $rs_time[$i] = $result;
  }
  foreach my $rs (@rs_time) {
    print $rs, "\n";
  }
}

# proper_ang($big)
sub proper_ang {
  my $big = $_[0];
  my $tmp = 0;
  if ($big > 0) {
    $tmp = $big / 360.0;
    $tmp = ($tmp - floor($tmp)) * 360.0;
  }
  else {
    $tmp = ceil(abs($big / 360.0));
    $tmp = $big + $tmp * 360.0;
  }
  return $tmp;
}
