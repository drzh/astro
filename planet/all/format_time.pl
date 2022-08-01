#!/usr/bin/perl

# Usage: prog FILE_htm

use strict;
use warnings;

my $fi = shift;

my @idx = (10, 11, 7, 5, 6, 4);

my @res = ();
$fi =~ /(\d{8})/;
my $date = $1;

open FILE, "<$fi" || die "Cannot open $fi: $!\n";
local $/ = '';
my $all = <FILE>;
if ($all =~ /(<table class="standardTable">(.+?)<\/table>)/) {
  my $rec = $1;
  foreach my $row ($rec =~ /<tr>(.*?)<\/tr>/g) {
    my @e = ($row =~ /<td.*?>(.*?)<\/td>/g);
    push @res, [ @e ];
  }
  print 'Date', "\t", 'Event', join("\t", @{$res[0]}), "\n";
  foreach my $i (@idx) {
    print join("\t", $date, @{$res[$i]}), "\n";
  }
}
close FILE;
