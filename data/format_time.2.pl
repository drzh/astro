#!/usr/bin/perl

# Usage: <STDIN> | prog INT_year | <STDOUT>

use strict;
use warnings;

my $year = shift;

while (<>) {
  chomp;
  /^ (\d+)/;
  my $day = $1;
  my @rs = /     (.{4})/g;
  foreach my $i (0 .. $#rs) {
    if ($rs[$i] eq '    ') {
      $rs[$i] = '-';
    }
    print $year, sprintf("%02d", $i + 1), $day, "\t", $rs[$i], "\n";
  }
}
