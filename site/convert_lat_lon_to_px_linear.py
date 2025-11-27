"""
Convert longitude and latitude to pixel coordinates.

Usage: python convert_lon_lat_to_px.py -i <FILE_site_lat_lon> -c <FILE_linear_correction> | <STDOUT>

FILE_site_lat_lon: site, latitude, longitude (no head, separated by tab)
FILE_linear_correction: lon, lat, x, y (no head, separated by tab)
"""

import sys
import click
import pandas as pd
import numpy as np
from scipy.stats import linregress

@click.command()
@click.option('-i', '--site_lat_lon', type=click.Path(exists=True), help='Site latitude and longitude')
@click.option('-c', '--linear_correction', type=click.Path(exists=True), help='Linear correction')

def convert_lat_lon_to_px_linear(site_lat_lon, linear_correction):
    # Read linear correction
    df_linear_correction = pd.read_csv(linear_correction, sep='\t', header=None)

    # name columns
    df_linear_correction.columns = ['lon', 'lat', 'x', 'y']

    # Run linear regression for longitude and x
    xslope, xintercept, x_r_value, x_p_value, x_std_err = linregress(df_linear_correction['lon'], df_linear_correction['x'])

    # Run linear regression for latitude and y
    yslope, yintercept, y_r_value, y_p_value, y_std_err = linregress(df_linear_correction['lat'], df_linear_correction['y'])

    # Read site longitude and latitude
    df_site_lat_lon = pd.read_csv(site_lat_lon, sep='\t', header=None)

    # name columns
    df_site_lat_lon.columns = ['site', 'lat', 'lon']

    # Convert longitude and latitude to pixel coordinates, and round to integer
    df_site_lat_lon['x'] = df_site_lat_lon['lon'] * xslope + xintercept
    df_site_lat_lon['y'] = df_site_lat_lon['lat'] * yslope + yintercept
    df_site_lat_lon['x'] = df_site_lat_lon['x'].round().astype(int)
    df_site_lat_lon['y'] = df_site_lat_lon['y'].round().astype(int)

    # Print result of site, x, y without index
    df_site_lat_lon[['site', 'x', 'y']].to_csv(sys.stdout, sep='\t', index=False, header=False)

if __name__ == '__main__':
    convert_lat_lon_to_px_linear()