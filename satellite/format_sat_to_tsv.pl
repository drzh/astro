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

my @e = ($all =~ /<option selected="selected".*?>(.*?)<\/option>/gs);
my $monday = (split / /, $e[1])[0] . ' '. $e[2];
$all =~ s#.*(<table class="standardTable">.+?</table>).*#$1#gs;
my $n = 0;
foreach my $r ($all =~ /(<tr.*?<\/tr>)/gs) {
  $n++;
  next if ($n == 1);
  $r =~ s%<a.*?>%%g;
  $r =~ s%</a>%%g;
  $r =~ s%[\n\r]\t*%%g;
  my @e = ($r =~ /<t[dh].*?>(.*?)<\/t[dh]>/gs);
  my $col1 = $sat;
  my $date = '';
  if ($n == 2) {
    $e[0] = 'Satellite';
    $date = 'Time';
  }
  else {
    $date = $monday . ' ' . $e[2];
    $date = Time::Piece -> strptime($date, '%B %e %H:%M:%S') -> strftime('%m/%d-%H:%M:%S');
  }
  print join("\t", $e[0], $date, @e[1 .. $#e]), "\n";
}
