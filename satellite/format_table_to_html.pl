#!/usr/bin/perl

use strict;
use warnings;
use Time::Piece;

undef $/;

my $all = <>;
$all =~ s#.*(<table bgcolor="gray" cellspacing="1" cellpadding="3">.+?</table>).*#$1#gs;
$all =~ s#(<tr.*?<td.*?>.*?</td>.*?<td.*?>)(.*?)(</td>)#$1.converttime($2).$3#ge;
$all =~ s#(<tr.*?<td.*?>.*?</td>.*?<td.*?>.*?</td>.*?<td.*?>.*?</td>.*?<td.*?>.*?</td>.*?<td.*?>.*?</td>.*?<td.*?>.*?</td>.*?<td.*?>.*?</td>.*?<td.*?>)(.*?)(</td>)#$1.converttime($2).$3#ge;
$all =~ s#<tr.*?AMSAT.*?</tr>##gs;
print $all, "\n";

sub converttime {
  my $timeutc = shift;
  my $timelocal = $timeutc;
  if ($timeutc =~ /\d\d:\d\d:\d\d/) {
    my $time = Time::Piece -> strptime($timeutc, '%H:%M:%S');
    my $timelocalo = localtime($time -> epoch);
    $timelocal = substr($timelocalo -> datetime, 11, 8)
  }
  return $timelocal . ' (' . $timeutc . ')';
  # return $timeutc;
}
