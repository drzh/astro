#!/usr/bin/perl

use strict;
use warnings;

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

my $datepre = '';
while (<>) {
  next if (! /^\d/);
  chomp;
  /^(.{4}) (.{3}) (.{2}).{14}(.{5}).{12}(.{5}).{12}(.{5})/;
  my ($y, $m, $d, $rise, $trans, $set) = ($1, $2, $3, $4, $5, $6, $7);
  my $date = $datepre;
  if ($y ne '    ') {
    $date = $y . $mon{$m} . $d;
  }
  if ($rise ne '     ') {
    $rise =~ s%:%%;
    print 'Rise', "\t", $date, "\t", $rise, "\n";
  }
  if ($set ne '     ') {
    $set =~ s%:%%;
    print 'Set', "\t", $date, "\t", $set, "\n";
  }
  $datepre = $date;
}
