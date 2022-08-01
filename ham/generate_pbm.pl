#!/usr/bin/perl

# Usage: <STDIN> | prog X_left X_right Y_up Y_down | <STDOUT>
# Usage: X Y is 0-base

use strict;
use warnings;

my $x1 = shift || -1;
my $x2 = shift || -1;
my $y1 = shift || -1;
my $y2 = shift || -1;

my %xstat;
my %ystat;
my $ypre = -1;

print 'P1', "\n";
print '# test.pbm', "\n";
my $rec = '';

while (<>) {
  next if (/^#/);
  chomp;
  my @e = /(\S+)/g;
  my ($x, $y) = ($e[0] =~ /(\d+)/g);
  if (($x1 == -1 || $x >= $x1) &&
      ($x2 == -1 || $x <= $x2) &&
      ($y1 == -1 || $y >= $y1) &&
      ($y2 == -1 || $y <= $y2)
     ) {
    $xstat{$x} = 1;
    $ystat{$y} = 1;
    if ($y == $ypre) {
      $rec .= ' ';
    }
    else {
      $ypre = $y;
      $rec .= "\n";
    }
    $rec .= ($e[3] eq 'srgb(4,2,4)') ? 0 : 1;
  }
}
print scalar(keys %xstat), ' ', scalar(keys %ystat);
print $rec;
