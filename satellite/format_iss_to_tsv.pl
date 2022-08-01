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
  next if ($n == 1);
  $r =~ s%<a.*?>%%g;
  $r =~ s%</a>%%g;
  $r =~ s%[\n\r]\t*%%g;
  my @e = ($r =~ /<t[dh].*?>(.*?)<\/t[dh]>/gs);
  my $col1 = $sat;
  if ($n == 2) {
    print join("\t", 'Satellite', 'Time', @e), "\n";
  }
  else {
    my $date = $e[0] . ' ' . $e[2];
    $date = Time::Piece -> strptime($date, '%d %b %H:%M:%S') -> strftime('%m/%d-%H:%M:%S');
    print join("\t", $col1, $date, @e[1 .. ($#e - 1)]), "\n";
  }
}
