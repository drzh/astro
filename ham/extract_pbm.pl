#!/usr/bin/perl

# Usage: <STDIN> | prog X_left X_right Y_up Y_down | <STDOUT>
# Usage: X Y is 0-base

use strict;
use warnings;

my $x1 = shift || -1;
my $x2 = shift || -1;
my $y1 = shift || -1;
my $y2 = shift || -1;

my $i = -3;
while (<>) {
  $i++;
  chomp;
  if ($i == -1) {
    my ($x, $y) = split / /;
    if ($x1 < 0) {
      $x1 = 0;
    }
    if ($x2 == -1 || $x2 > $x - 1) {
      $x2 = $x - 1;
    }
    if ($y1 < 0) {
      $y1 = 0;
    }
    if ($y2 == -1 || $y2 > $y - 1) {
      $y2 = $y - 1;
    }
    print 'P1', "\n", $x2 - $x1 + 1, ' ', $y2 - $y1 + 1, "\n";
  }
  elsif ($i >= $y1 && $i <= $y2) {
    my @e = /(\S+)/g;
    print join(' ', @e[$x1 .. $x2]) . "\n";
  }
}
