#!/usr/bin/perl

use strict;
use warnings;

$/='';
my $rec = <>;
foreach my $ele ($rec =~ /<m:properties>(.+?)<\/m:properties>/gs) {
  my $t = '';
  foreach my $e ($ele =~ /(<d:.+?<\/d:.+?>)/g) {
    if ($e =~ /<d:(\S+) .+>(\S+?)<\/d:\S+>/) {
      my ($k, $v) = ($1, $2);
      if ($k eq 'NEW_DATE') {
        $t = substr($v, 0, 10);
      }
      elsif ($k =~ /BC_(\S+)/) {
        my $term = $1;
        if ($term eq '1YEAR' ||
            $term eq '2YEAR' ||
            $term eq '5YEAR' ||
            $term eq '10YEAR' ||
            $term eq '30YEAR' ||
	    $term eq '3MONTH'
           ) {
          print join("\t", $t, $v, $term), "\n";
        }
      }
    }
  }
}
