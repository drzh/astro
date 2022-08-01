#!/usr/bin/perl

# Usage: prog FILE1.table.html FILE2.talbe.html ...

use strict;
use warnings;
use Time::Piece;

my @fs = @ARGV[1 .. $#ARGV];

my $max = 200;

undef $/;

my @rec;
my $th = '';
foreach my $f (@fs) {
  my $sat = (split /\./, $f)[1];
  open FILE, "<$f"
    || die "Cannot open $f: $!\n";
  my $all = <FILE>;
  close FILE;
  if ($th eq '') {
    $all =~ /(<tr[^>]*>)\s*(<th.*?<\/tr>)/gs;
    $th = $1 . '<th>Satellite</th>' . $2;
  }
  foreach my $r ($all =~ /(<tr.*?<\/tr>)/g) {
    my @e = ($r =~ /<td[^>]*>(.*?)<\/td>/g);
    my $date = Time::Piece -> strptime($e[0], '%d %b %y');
    push @rec, [ $sat, $date -> strftime('%y-%m-%d'), @e[1 .. $#e] ];
  }
}

print '<table bgcolor="gray" cellspacing="1" cellpadding="3">', $th;
@rec = sort {$a -> [1] cmp $b -> [1] || $a -> [2] cmp $b -> [2]} @rec;
my $datetimenow = localtime();
my $n = 0;
foreach my $i (0 .. $#rec) {
  my @e = @{$rec[$i]};
  $e[2] =~ /\((\d\d:\d\d:\d\d)\)/;
  my $timeutc = $1;
  my $datetime = Time::Piece -> strptime($e[1] . ' ' . $timeutc, '%y-%m-%d %H:%M:%S');
  if ($datetime > $datetimenow) {
    $e[1] = Time::Piece -> strptime($e[1], '%y-%m-%d') -> strftime('%a, %m/%d');
    if ($i % 2 == 1) {
      print '<tr align="center" bgcolor="gainsboro">';
    }
    else {
      print '<tr align="center" bgcolor="white">';
      # print '<tr>';
    }
    foreach my $j (0 .. $#e) {
      print '<td>', $e[$j], '</td>';
    }
    print '</tr>';
    last if (++$n == $max);
  }
}
print '</table>';
