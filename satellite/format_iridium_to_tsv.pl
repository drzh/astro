#!/usr/bin/perl

# Usage: prog FILE_html

use strict;
use warnings;
use Time::Piece;

my $fi = shift;

my $sat = (split /\./, $fi)[0];
undef $/;
open FILE, "<$fi"
  || die "Cannot open $fi: $!\n";
my $all = <FILE>;
close FILE;

$all =~ s#.*(<table class="standardTable">.+?</table>).*#$1#gs;
my $n = 0;
foreach my $r ($all =~ /(<tr.*?<\/tr>)/gs) {
  $n++;
  $r =~ s%<a.*?>%%g;
  $r =~ s%</a>%%g;
  $r =~ s%<img.*?>%%g;
  $r =~ s%[\n\r]\t*%%g;
  my @e = ($r =~ /<t[dh].*?>(.*?)<\/t[dh]>/gs);
  my $col1 = $sat;
  my $date = $e[0];
  if ($n == 1) {
    $col1 = 'Satellite';
    $date = 'Time';
  }
  else {
    $date = $e[0];
    $date = Time::Piece -> strptime($date, '%b %d, %H:%M:%S') -> strftime('%m/%d-%H:%M:%S');
    $e[0] =~ s%.+, %%;
  }
  print join("\t", $col1, $date, $e[1], $e[0], @e[2 .. $#e]), "\n";
}
