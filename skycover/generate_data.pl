#!/usr/bin/perl

# Usage: prog

use strict;
use warnings;

my @cmddl = ('curl https://tgftp.nws.noaa.gov/SL.us008001/ST.opnl/DF.gr2/DC.ndfd/AR.conus/VP.001-003/ds.sky.bin > ds.sky.bin.1',
            'curl https://tgftp.nws.noaa.gov/SL.us008001/ST.opnl/DF.gr2/DC.ndfd/AR.conus/VP.004-007/ds.sky.bin > ds.sky.bin.2',
            'curl https://tgftp.nws.noaa.gov/SL.us008001/ST.opnl/DF.gr2/DC.ndfd/AR.conus/VP.001-003/ds.rhm.bin > ds.rhm.bin.1',
            'curl https://tgftp.nws.noaa.gov/SL.us008001/ST.opnl/DF.gr2/DC.ndfd/AR.conus/VP.004-007/ds.rhm.bin > ds.rhm.bin.2',
            'curl https://tgftp.nws.noaa.gov/SL.us008001/ST.opnl/DF.gr2/DC.ndfd/AR.conus/VP.001-003/ds.temp.bin > ds.temp.bin.1',
            'curl https://tgftp.nws.noaa.gov/SL.us008001/ST.opnl/DF.gr2/DC.ndfd/AR.conus/VP.004-007/ds.temp.bin > ds.temp.bin.2',
           );

# download data
foreach my $c (@cmddl) {
  system($c);
}

my @fileds = ('ds.sky.bin.1', 'ds.sky.bin.2');
my $files = '../site/site.php';
my $fileo = 'all.skycover.3day.UTC.format';
process(\@fileds, $files, $fileo);

@fileds = ('ds.rhm.bin.1', 'ds.rhm.bin.2');
$files = '../site/site.php';
$fileo = 'all.rhm.3day.UTC.format';
process(\@fileds, $files, $fileo);

@fileds = ('ds.temp.bin.1', 'ds.temp.bin.2');
$files = '../site/site.php';
$fileo = 'all.temp.3day.UTC.format';
process(\@fileds, $files, $fileo);

sub process {
  my ($fileds, $files, $fileo) = @_;

  # delete the outfile if it exists
  if (-e $fileo) {
    unlink $fileo;
  }

  local $/;
  open FILE, "<$files"
    || die "Cannot open $files: $!\n";
  my $all = <FILE>;
  close FILE;

  my @sites;
  my @lons;
  my @lats;
  my $loncmd = '';

  foreach my $arr ($all =~ /(array\([^\(]+?\))/gs) {
    if ($arr =~ /'(.+?)',(\s*)(\S+?),(\s*)(\S+?),/) {
      my ($site, $lat, $lon) = ($1, $3, $5);
      push @sites, $site;
      push @lons, $lon;
      push @lats, $lat;
      $loncmd .= ' -lon ' . $lon . ' ' . $lat;
    }
  }

  foreach my $filed (@{$fileds}) {
    my @times;
    # my $cmd = '/home/celaeno/usr/bin/wgrib2 -V ' . $filed . ' | grep -P \'^\d\' | perl -npe \'s#.*:vt=(\d+):.*#${1}00#\'';
    my $cmd = '/home/celaeno/usr/bin/wgrib2 -V ' . $filed;
    open FILE, $cmd . ' |'
      || die $!;
    my $line = <FILE>;
    foreach my $t (split /\n/, $line) {
      if ($t =~ /^\d/) {
        $t =~ s#.*:vt=(\d+):.*#${1}00#;
        push @times, $t;
      }
    }
    close FILE;

    $cmd = '/home/celaeno/usr/bin/wgrib2 ' . $filed . $loncmd;
    open FILE, $cmd . ' |'
      || die $!;
    $line = <FILE>;
    my @rec = split /\n/, $line;
    close FILE;

    if (scalar(@times) != scalar(@rec)) {
      die "# of times is not equal to # of record\n";
    }

    open FILE, ">>$fileo"
      || die "$!\n";
    foreach my $i (0 .. $#rec) {
      my @e = split /:/, $rec[$i];
      @e = @e[2 .. $#e];
      foreach my $j (0 .. $#e) {
        $e[$j] =~ s#.*,val=(\d+).*#$1#;
        print FILE join("\t", $sites[$j], $times[$i], $e[$j]), "\n";
      }
    }
    close FILE;
  }
}
