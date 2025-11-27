#!/usr/bin/perl

# Usage: prog

use strict;
use warnings;

my @fileds = ('ds.sky.bin.1', 'ds.sky.bin.2');
#my @fileds = ('ds.sky.bin.1');
print 'curl https://tgftp.nws.noaa.gov/SL.us008001/ST.opnl/DF.gr2/DC.ndfd/AR.conus/VP.001-003/ds.sky.bin > ds.sky.bin.1', "\n";
print 'curl https://tgftp.nws.noaa.gov/SL.us008001/ST.opnl/DF.gr2/DC.ndfd/AR.conus/VP.004-007/ds.sky.bin > ds.sky.bin.2', "\n";
print 'if [ `du -s ds.sky.bin.1 | cut -f1` -gt 10000 ] ; then', "\n";
my $files = '../site/site.pos';
my $fileo = 'all.skycover.3day.UTC.format';
process(\@fileds, $files, $fileo);
#$files = '../eclipse2017/site.php';
#$fileo = '../eclipse2017/skycover/all.skycover.3day.UTC.format';
#process(\@fileds, $files, $fileo);
print 'fi', "\n";

@fileds = ('ds.rhm.bin.1', 'ds.rhm.bin.2');
#@fileds = ('ds.rhm.bin.1');
print 'curl https://tgftp.nws.noaa.gov/SL.us008001/ST.opnl/DF.gr2/DC.ndfd/AR.conus/VP.001-003/ds.rhm.bin > ds.rhm.bin.1', "\n";
print 'curl https://tgftp.nws.noaa.gov/SL.us008001/ST.opnl/DF.gr2/DC.ndfd/AR.conus/VP.004-007/ds.rhm.bin > ds.rhm.bin.2', "\n";
print 'if [ `du -s ds.rhm.bin.1 | cut -f1` -gt 10000 ] ; then', "\n";
#$files = '../site/site.pos';
$fileo = 'all.rhm.3day.UTC.format';
process(\@fileds, $files, $fileo);
print 'fi', "\n";

@fileds = ('ds.temp.bin.1', 'ds.temp.bin.2');
#@fileds = ('ds.temp.bin.1');
print 'curl https://tgftp.nws.noaa.gov/SL.us008001/ST.opnl/DF.gr2/DC.ndfd/AR.conus/VP.001-003/ds.temp.bin > ds.temp.bin.1', "\n";
print 'curl https://tgftp.nws.noaa.gov/SL.us008001/ST.opnl/DF.gr2/DC.ndfd/AR.conus/VP.004-007/ds.temp.bin > ds.temp.bin.2', "\n";
print 'if [ `du -s ds.temp.bin.1 | cut -f1` -gt 10000 ] ; then', "\n";
#$files = '../site/site.pos';
$fileo = 'all.temp.3day.UTC.format';
process(\@fileds, $files, $fileo);
print 'fi', "\n";

sub process {
  my ($fileds, $files, $fileo) = @_;
  my $fileotmp = $fileo . '.tmp';
  my @site;
  open FILE, "<$files"
    || die "Cannot open $files: $!\n";
  while (<FILE>) {
    chomp;
    push @site, [ split /\t/, $_ ];
  }

  #my $all = <FILE>;
  close FILE;
  print 'touch ', $fileotmp, "\n";
  foreach my $filed (@{$fileds}) {
    print '/home/celaeno/usr/bin/wgrib2 -V ', $filed, ' | grep -P \'^\d\' | perl -npe \'s#.*:vt=(\d+):.*#${1}00#\' > tmp.time', "\n";
    #foreach my $arr ($all =~ /(array\([^\(]+?\))/gs) {
    foreach my $s (@site) {
	    #if ($arr =~ /'(.+?)',(\s*)(\S+?),(\s*)(\S+?),/) {
	    #my ($site, $lat, $long) = ($1, $3, $5);
      my ($site, $lat, $long) = @{$s};
      print '/home/celaeno/usr/bin/wgrib2 ', $filed, ' -lon ', $long, ' ', $lat, ' | perl -npe \'s#.*,val=(\d+).*#$1#\' > tmp.val', "\n";
      print 'paste tmp.time tmp.val | awk \'{print "', $site, '\t"$0}\' >> ', $fileotmp, "\n";
	#}
    }
  }
  print 'mv ', $fileotmp, ' ', $fileo, "\n";
  print 'rm tmp.val tmp.time', "\n";
}
