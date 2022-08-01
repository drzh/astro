#!/usr/bin/perl

use strict;
use warnings;

my $year = shift || '';

my %mon = (
           'Jan' => '01',
           'Feb' => '02',
           'Mar' => '03',
           'Apr' => '04',
           'May' => '05',
           'Jun' => '06',
           'Jul' => '07',
           'Aug' => '08',
           'Sep' => '09',
           'Oct' => '10',
           'Nov' => '11',
           'Dec' => '12'
          );

local $/ = undef;
my $all = <>;
foreach my $e ($all =~ /<div style="margin-left:2em"><pre>(.+?)<\/pre>/gs) {
  $e =~ s%<.+?>%%g;
  my @rec = split /\n/, $e;
  my $month = '   ';
  # print join("\n", @rec[0 .. 2]), "\n";
  foreach my $i (3 .. ($#rec - 3)) {
    if ($rec[$i] =~ /^(...) (.\d) (\d\d):(\d\d)   (.+)/) {
      if ($1 ne '   ') {
        $month = $mon{$1};
      }
      my $day = $2;
      my $hr = $3;
      my $min = $4;
      my $cont = $5;
      $day =~ s% %0%g;
      print $year, $month, $day, "\t", $hr, $min, "\t", $cont, "\n";
    }
    # print $rec[$i], "\n";
  }
  # print join("\n", @rec[($#rec - 2) .. $#rec]), "\n";
}
