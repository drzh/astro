#!/usr/bin/perl

# Usage: prog FILE_html

use strict;
use warnings;
use Time::Piece;

my $fi = shift;

my $sat = (split /\./, $fi)[1];
undef $/;
open FILE, "<$fi"
  || die "Cannot open $fi: $!\n";
my $all = <FILE>;
close FILE;

$all =~ s#.*(<table bgcolor="gray" cellspacing="1" cellpadding="3">.+?</table>).*#$1#gs;
$all =~ s#<tr.*?AMSAT.*?</tr>##gs;
my $n = 0;
foreach my $r ($all =~ /(<tr.*?<\/tr>)/gs) {
  my @e = ($r =~ /<t[dh].*?>(.*?)<\/t[dh]>/gs);
  my $col1 = $sat;
  if ($n == 0) {
    $n = 1;
    $col1 = 'Satellite';
  }
  else {
    $e[0] = Time::Piece -> strptime($e[0], '%d %b %y') -> strftime('%y-%m-%d');
  }
  print join("\t", $col1, @e), "\n";
}

sub converttime {
  my $timeutc = shift;
  my $timelocal = $timeutc;
  if ($timeutc =~ /\d\d:\d\d:\d\d/) {
    my $time = Time::Piece -> strptime($timeutc, '%H:%M:%S');
    my $timelocalo = localtime($time -> epoch);
    $timelocal = substr($timelocalo -> datetime, 11, 8)
  }
  return $timelocal . ' (' . $timeutc . ')';
  # return $timeutc;
}
