#!/usr/bin/perl

use strict;
use warnings;

my $c1 = "miniobsshown=1&mlevel=2&startDay=";
# 20;
my $c2 = "&startMonth=";
# 5;
my $c3 = "&startc=";
# 20;
my $c4 = "&startd=";
# 1;
my $c5 = "&starti=";
# 7;
my $c6 = "&startad=";
# 0;
my $c7 = "&startHour=";
# 0;
my $c8 = "&startMin=";
# 0;
my $c9 = "&startSec=";
# 0;
my $c10 = "&Go.x=20&Go.y=16&il_showsection=on&confighide=999&size=40&rendaperture=0&pattern=on&useDSS=on&pt=ff130c2&sub=1&wwidth=1600&messageseensite=&renbuild=&sec=7&cha=7&obs=34251047002999&timebuild=&objectname=&glosscheck=1&showglos=on&obsbuild=2&m=";

# my $c10 = "&Go.x=13&Go.y=10&il_showsection=on&confighide=999&size=480&moons=on&rendaperture=0&horizontal=on&objectname=&glosscheck=1&showglos=on&pt=ff130c4&cha=7&m=&timebuild=&messageseensite=&obsbuild=2&pattern=on&wwidth=1920&sec=7&sub=1&renbuild=&useDSS=on&obs=48468476797370";

while (<>) {
  chomp;
  /(\d\d)(\d)(\d)(\d\d)(\d\d)/;
  my $c = $1;
  my $d = $2;
  my $i = $3;
  my $mon = $4;
  my $day = $5;
  $mon =~ s%^0%%;
  $day =~ s%^0%%;
  foreach my $hr (0, 6, 12, 18) {
    print 'curl "https://www.calsky.com/observer//cscss3.php?1561666922.33532" -H "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:67.0) Gecko/20100101 Firefox/67.0" -H "Accept: text/css,*/*;q=0.1" -H "Accept-Language: en-US,en;q=0.5" --compressed -H "Connection: keep-alive" --data "', $c1, $day, $c2, $mon, $c3, $c, $c4, $d, $c5, $i, $c6, 0, $c7, $hr, $c8, 0, $c9, 0, $c10, '" https://www.calsky.com > ', $_, '.', $hr, '.htm', "\n";
  }
}
