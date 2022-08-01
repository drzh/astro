#!/usr/bin/perl

# Usage: <STDIN> | prog | <STDOUT>

use strict;
use warnings;

my @group1 = ('3MONTH', '1YEAR', '2YEAR', '5YEAR', '10YEAR', '30YEAR');
my @group2 = ('3MONTH');

my %rec;
while (<>) {
  chomp;
  my @e = split /\t/;
  $rec{$e[0] . "\t" . $e[1]}{$e[3]} = $e[2];
}

foreach my $k (sort keys % rec) {
  foreach my $g1 (@group1) {
    if (exists $rec{$k}{$g1}) {
      foreach my $g2 (@group2) {
        if (exists $rec{$k}{$g2}) {
          print join("\t", $k, $rec{$k}{$g1} - $rec{$k}{$g2}, $g1 . '-' . $g2), "\n";
        }
      }
    }
  }
}
