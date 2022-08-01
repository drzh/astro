#!/usr/bin/perl

# Usage: <STDIN> | prog | <STDOUT>

use strict;
use warnings;

my %band;
my %es;
my @tag;
my @value;

while (<>) {
  chomp;
  if (/<band name="(\S+?)" time="(\S+?)">(\S+?)<\/band>/) {
    $band{$1}{$2} = $3;
  }
  elsif (/phenomenon name="E-Skip" location="(\S+?)">(\S+?)<\/phenomenon>/) {
    $es{$1} = $2;
  }
  elsif (/^\t\t<(\S+?)> *(.+?)<\/(\S+?)>/) {
    push @tag, $1;
    push @value, $2;
  }
}

my @bd = ('80m-40m', '30m-20m', '17m-15m', '12m-10m');
foreach my $b (@bd) {
  print join("\t", $b, $band{$b}{'day'}, $band{$b}{'night'}), "\n";
}

foreach my $i (0 .. $#tag) {
  print $tag[$i], "\t", $value[$i], "\n";
}
