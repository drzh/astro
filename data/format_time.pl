#!/usr/bin/perl

# Usage: <STDIN> | prog INT_year | <STDOUT>

use strict;
use warnings;

my $year = shift;

while (<>) {
  chomp;
  /^(\d+)/;
  my $day = $1;
  my @rs = /  (.{4} .{4})/g;
  foreach my $i (0 .. $#rs) {
    my ($r, $s) = ($rs[$i] =~ /(.{4}) (.{4})/);
    if ($r eq '    ') {
      $r = '-';
    }
    if ($s eq '    ') {
      $s = '-';
    }
    print $year, sprintf("%02d", $i + 1), $day, "\t", $r, "\t", $s, "\n";
  }
}
