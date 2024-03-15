#!/bin/bash
cat site.eclipse2024.pos | python3 calc_proj_fulldisk.py | cat site.general.fulldisk.proj - > site.eclipse2024.fulldisk.proj
