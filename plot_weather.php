<?php
$weather_plot_endpoint = 'weather_plot_data.php?' . http_build_query(
    array(
        'f1' => (string) $f1,
        'f2' => (string) $f2,
        'f3' => (string) $f3,
    )
);
?>
<link rel="stylesheet" href="https://unpkg.com/uplot@1.6.31/dist/uPlot.min.css">
<section class="panel">
  <div class="weather-plot-legend" aria-label="Weather plot series">
    <button type="button" class="weather-plot-legend__item weather-plot-legend__toggle" data-weather-plot-toggle="sky_cover" aria-pressed="true"><span class="weather-plot-legend__dot weather-plot-legend__dot--sky"></span><span>SkyCover</span></button>
    <button type="button" class="weather-plot-legend__item weather-plot-legend__toggle" data-weather-plot-toggle="humidity" aria-pressed="true"><span class="weather-plot-legend__dot weather-plot-legend__dot--humidity"></span><span>Humidity</span></button>
    <button type="button" class="weather-plot-legend__item weather-plot-legend__toggle" data-weather-plot-toggle="temperature_plot" aria-pressed="true"><span class="weather-plot-legend__dot weather-plot-legend__dot--temperature"></span><span>Temp</span></button>
  </div>
</section>
<section class="panel">
  <div
    id="weather-plot-root"
    class="weather-plot-root"
    data-endpoint="<?php echo htmlspecialchars($weather_plot_endpoint, ENT_QUOTES, 'UTF-8'); ?>"
  >
    <p class="weather-plot-status">Loading weather plots...</p>
  </div>
</section>
<script src="https://unpkg.com/uplot@1.6.31/dist/uPlot.iife.min.js"></script>
<script>
(() => {
  const root = document.getElementById('weather-plot-root');
  if (!root) {
    return;
  }

  const endpoint = root.dataset.endpoint || '';
  const legendButtons = Array.from(document.querySelectorAll('[data-weather-plot-toggle]'));
  const weatherPlots = [];
  const seriesVisibility = {
    sky_cover: true,
    humidity: true,
    temperature_plot: true
  };
  const SERIES_INDEX = {
    sky_cover: 1,
    humidity: 2,
    temperature_plot: 3
  };
  const SERIES_STYLE = {
    sky_cover: { stroke: 'firebrick', fill: 'firebrick', size: 6, label: 'SkyCover' },
    humidity: { stroke: 'green', fill: 'green', size: 2, label: 'Humidity' },
    temperature_plot: { stroke: 'orange', fill: 'orange', size: 2, label: 'Temp' }
  };

  function setStatus(message) {
    root.innerHTML = `<p class="weather-plot-status">${message}</p>`;
  }

  function pointSeries(metricKey, showAxis) {
    const style = SERIES_STYLE[metricKey];
    return {
      label: style.label,
      stroke: style.stroke,
      width: 0,
      paths: () => null,
      points: {
        show: true,
        size: style.size,
        stroke: style.stroke,
        fill: style.fill,
        width: 1
      },
      scale: showAxis ? 'y' : 'hidden'
    };
  }

  function updateLegendButtons() {
    legendButtons.forEach((button) => {
      const key = button.dataset.weatherPlotToggle;
      const visible = seriesVisibility[key] !== false;
      button.classList.toggle('is-active', visible);
      button.setAttribute('aria-pressed', visible ? 'true' : 'false');
    });
  }

  function applySeriesVisibility(plot) {
    Object.keys(SERIES_INDEX).forEach((key) => {
      plot.setSeries(SERIES_INDEX[key], { show: seriesVisibility[key] !== false }, false);
    });
  }

  legendButtons.forEach((button) => {
    button.addEventListener('click', () => {
      const key = button.dataset.weatherPlotToggle;
      if (!Object.prototype.hasOwnProperty.call(seriesVisibility, key)) {
        return;
      }
      seriesVisibility[key] = !seriesVisibility[key];
      updateLegendButtons();
      weatherPlots.forEach(applySeriesVisibility);
    });
  });
  updateLegendButtons();

  function tempAxisLabel(value) {
    return (((value / 100) * 50) - 10).toFixed(0);
  }

  function extractHourLabel(label) {
    const hourMinute = extractHourMinute(label);
    if (!hourMinute) {
      return '';
    }
    return String(hourMinute.hour);
  }

  function extractHourMinute(label) {
    const match = String(label || '').match(/(\d{1,2}):(\d{2})$/);
    if (!match) {
      return null;
    }
    return {
      hour: Number(match[1]),
      minute: Number(match[2])
    };
  }

  function extractHourNumber(label) {
    const hourMinute = extractHourMinute(label);
    if (!hourMinute) {
      return null;
    }
    return hourMinute.hour;
  }

  function computeTickIndexes(x, xLabels, width) {
    const tickIndexes = [];
    let lastTickOffset = null;
    const hourStep = isMobileWeatherPlot(width) ? 6 : 3;

    x.forEach((value, idx) => {
      const hourMinute = extractHourMinute(xLabels[idx]);
      if (!hourMinute) {
        return;
      }

      if (tickIndexes.length === 0) {
        if (hourMinute.minute !== 0 || hourMinute.hour % hourStep !== 0) {
          return;
        }
      } else if (value - lastTickOffset < hourStep) {
        return;
      }

      tickIndexes.push(idx);
      lastTickOffset = value;
    });

    return tickIndexes;
  }

  function dayLabelAtOffset(dayOffset, x, xLabels) {
    let closestIndex = -1;
    let closestDistance = Infinity;
    x.forEach((value, idx) => {
      const distance = Math.abs(value - dayOffset);
      if (distance < closestDistance) {
        closestDistance = distance;
        closestIndex = idx;
      }
    });
    if (closestIndex === -1) {
      return '';
    }
    return xLabels[closestIndex] ? xLabels[closestIndex].replace(/ \d{1,2}:\d{2}$/, '') : '';
  }

  function dayLabelBeforeOffset(dayOffset, x, xLabels) {
    for (let idx = x.length - 1; idx >= 0; idx -= 1) {
      if (x[idx] < dayOffset) {
        return xLabels[idx] ? xLabels[idx].replace(/ \d{1,2}:\d{2}$/, '') : '';
      }
    }
    return '';
  }

  function dayLabelAfterOffset(dayOffset, x, xLabels) {
    for (let idx = 0; idx < x.length; idx += 1) {
      if (x[idx] >= dayOffset) {
        return xLabels[idx] ? xLabels[idx].replace(/ \d{1,2}:\d{2}$/, '') : '';
      }
    }
    return '';
  }

  function isMobileWeatherPlot(width) {
    return width <= 520 || window.matchMedia('(max-width: 640px)').matches;
  }

  function getWeatherAxisFontSize(width) {
    return isMobileWeatherPlot(width) ? 14 : 12;
  }

  function createCard(site, layout) {
    const panel = document.createElement('section');
    panel.className = 'panel weather-plot-panel';

    const header = document.createElement('div');
    header.className = 'weather-card__header weather-card__header--compact';

    const title = document.createElement('h2');
    title.className = 'weather-card__title weather-card__title--compact weather-plot-title';
    const coordText = `${Number(site.latitude).toFixed(4)}, ${Number(site.longitude).toFixed(4)}`;
    const coordLink = document.createElement('a');
    coordLink.href = `https://maps.google.com/maps?q=${site.latitude},${site.longitude}`;
    coordLink.target = '_blank';
    coordLink.rel = 'noopener noreferrer';
    coordLink.textContent = coordText;
    const coordWrap = document.createElement('span');
    coordWrap.className = 'weather-card__meta weather-card__meta--compact weather-card__meta--inline';
    coordWrap.appendChild(coordLink);

    if (site.clear_dark_sky_link) {
      const link = document.createElement('a');
      link.href = site.clear_dark_sky_link;
      link.target = '_blank';
      link.rel = 'noopener noreferrer';
      link.className = 'weather-card__title-link--inline';
      link.textContent = site.name || 'Site';
      title.appendChild(link);
    } else {
      title.textContent = site.name || 'Site';
    }
    title.appendChild(document.createTextNode(' ('));
    title.appendChild(coordWrap);
    title.appendChild(document.createTextNode(')'));

    header.appendChild(title);
    panel.appendChild(header);

    const figure = document.createElement('figure');
    figure.className = 'media-panel image-scroll weather-plot-frame';
    const canvas = document.createElement('div');
    canvas.className = 'weather-plot-canvas';
    figure.appendChild(canvas);
    const tooltip = document.createElement('div');
    tooltip.className = 'weather-plot-tooltip';
    tooltip.hidden = true;
    figure.appendChild(tooltip);
    panel.appendChild(figure);

    const x = Array.isArray(site.x) ? site.x : [];
    const xLabels = Array.isArray(site.x_labels) ? site.x_labels : [];
    const sky = site.series && Array.isArray(site.series.sky_cover) ? site.series.sky_cover : [];
    const humidity = site.series && Array.isArray(site.series.humidity) ? site.series.humidity : [];
    const tempPlot = site.series && Array.isArray(site.series.temperature_plot) ? site.series.temperature_plot : [];
    const tempReal = site.series && Array.isArray(site.series.temperature_c) ? site.series.temperature_c : [];
    const softText = getComputedStyle(document.documentElement).getPropertyValue('--text-soft').trim() || '#b3c2cc';
    const plotWidth = Math.max(320, Math.round(figure.clientWidth || panel.clientWidth || root.clientWidth || Number(layout.width) || 873));
    const maxX = Math.max(...x, 168);
    const startHour = extractHourNumber(xLabels[0]);
    const axisFontSize = getWeatherAxisFontSize(plotWidth);
    const mobileWeatherPlot = isMobileWeatherPlot(plotWidth);
    const xAxisLabelOffset = mobileWeatherPlot ? 4 : 5;
    const xAxisBottomMargin = 2;
    const yAxisFontSize = isMobileWeatherPlot(plotWidth) ? 14 : 11;
    const yAxisSize = yAxisFontSize + 14;

    const opts = {
      width: plotWidth,
      height: Number(layout.height) || 218,
      padding: [null, 0, null, 0],
      legend: { show: false },
      select: { show: false },
      cursor: { drag: { x: false, y: false, setScale: false } },
      scales: {
        x: { time: false, range: [0, maxX] },
        y: { range: [0, 100] },
        hidden: { range: [0, 100] }
      },
      series: [
        {},
        pointSeries('sky_cover', true),
        pointSeries('humidity', false),
        pointSeries('temperature_plot', false)
      ],
      axes: [
        {
          stroke: 'rgba(179, 194, 204, 0.88)',
          grid: { show: false },
          values: () => [],
          size: axisFontSize + xAxisLabelOffset + xAxisBottomMargin
        },
        {
          stroke: 'rgba(179, 194, 204, 0.88)',
          grid: { show: false },
          ticks: { show: false },
          splits: () => [0, 20, 40, 60, 80, 100],
          values: () => ['0', '20', '40', '60', '80', '100'],
          font: `${yAxisFontSize}px sans-serif`,
          gap: 5,
          size: yAxisSize
        },
        {
          side: 1,
          scale: 'hidden',
          stroke: 'rgba(179, 194, 204, 0.88)',
          grid: { show: false },
          ticks: { show: false },
          splits: () => [0, 20, 40, 60, 80, 100],
          values: (self, splits) => splits.map((value) => tempAxisLabel(value)),
          font: `${yAxisFontSize}px sans-serif`,
          gap: 5,
          size: yAxisSize
        }
      ],
      hooks: {
        drawClear: [
          (u) => {
            const ctx = u.ctx;
            const top = u.bbox.top;
            const height = u.bbox.height;
            if (startHour == null) {
              return;
            }

            ctx.save();
            for (let hour = 0; hour < Math.ceil(maxX); hour += 1) {
              const midpointHour = (startHour + hour + 0.5) % 24;
              const fill = midpointHour >= 6 && midpointHour < 18
                ? 'rgba(179, 194, 204, 0.07)'
                : 'rgba(7, 19, 29, 0.34)';
              const xStart = u.valToPos(hour, 'x', true);
              const xEnd = u.valToPos(Math.min(hour + 1, maxX), 'x', true);
              ctx.fillStyle = fill;
              ctx.fillRect(xStart, top, Math.max(0, xEnd - xStart), height);
            }
            ctx.restore();
          }
        ],
        drawAxes: [
          (u) => {
            const ctx = u.ctx;
            ctx.save();
            ctx.strokeStyle = 'rgba(179, 194, 204, 0.88)';
            ctx.lineWidth = 1;
            const mobilePlot = isMobileWeatherPlot(u.bbox.width);
            const xAxisFontSize = getWeatherAxisFontSize(u.bbox.width);
            const xAxisLabelOffset = mobilePlot ? 4 : 5;
            const dayLabelFontSize = mobilePlot ? 14 : 11;
            const dayLabelOffset = mobilePlot ? 10 : 6;
            const tickIndexes = computeTickIndexes(x, xLabels, u.bbox.width);
            ctx.textBaseline = 'alphabetic';
            const top = u.bbox.top;
            const bottom = u.bbox.top + u.bbox.height;
            const left = u.bbox.left;
            const right = u.bbox.left + u.bbox.width;
            [0, 20, 40, 60, 80, 100].forEach((value) => {
              const yPos = Math.round(u.valToPos(value, 'y', true));
              ctx.beginPath();
              ctx.moveTo(left, yPos);
              ctx.lineTo(right, yPos);
              ctx.setLineDash([2, 4]);
              ctx.stroke();
              ctx.setLineDash([]);
            });
            ctx.setLineDash([]);
            ctx.beginPath();
            ctx.moveTo(left, top);
            ctx.lineTo(left, bottom);
            ctx.moveTo(right, top);
            ctx.lineTo(right, bottom);
            ctx.stroke();
            ctx.font = `${xAxisFontSize}px sans-serif`;
            tickIndexes.forEach((idx) => {
              const hourLabel = extractHourLabel(xLabels[idx]);
              const hourX = Math.round(u.valToPos(x[idx], 'x', true));
              ctx.beginPath();
              ctx.moveTo(hourX, bottom);
              ctx.lineTo(hourX, bottom + 4);
              ctx.stroke();
              ctx.textAlign = 'center';
              ctx.textBaseline = 'top';
              ctx.fillStyle = softText;
              ctx.fillText(hourLabel, hourX, bottom + xAxisLabelOffset);
            });
            if (startHour != null) {
              const firstDayOffset = (24 - startHour) % 24;
              for (let dayOffset = firstDayOffset; dayOffset <= maxX; dayOffset += 24) {
                const leftDayLabel = dayLabelBeforeOffset(dayOffset, x, xLabels);
                const rightDayLabel = dayLabelAfterOffset(dayOffset, x, xLabels) || dayLabelAtOffset(dayOffset, x, xLabels);
                if (!leftDayLabel && !rightDayLabel) {
                  continue;
                }
                const dayX = Math.round(u.valToPos(dayOffset, 'x', true));
                ctx.beginPath();
                ctx.moveTo(dayX, top - (dayLabelFontSize + 6));
                ctx.lineTo(dayX, bottom);
                ctx.setLineDash([3, 5]);
                ctx.stroke();
                ctx.setLineDash([]);
                ctx.font = `${dayLabelFontSize}px sans-serif`;
                ctx.textBaseline = 'bottom';
                ctx.fillStyle = softText;
                if (leftDayLabel) {
                  ctx.textAlign = 'right';
                  ctx.fillText(leftDayLabel, dayX - dayLabelOffset, top - 4);
                }
                if (rightDayLabel) {
                  ctx.textAlign = 'left';
                  ctx.fillText(rightDayLabel, dayX + dayLabelOffset, top - 4);
                }
                ctx.textBaseline = 'alphabetic';
              }
            }
            ctx.restore();
          }
        ],
        setCursor: [
          (u) => {
            const idx = u.cursor.idx;
            if (idx == null || idx < 0 || idx >= xLabels.length) {
              tooltip.hidden = true;
              tooltip.textContent = '';
              return;
            }

            const candidates = [
              { key: 'sky_cover', label: 'SkyCover', value: sky[idx], suffix: '%', scale: 'y' },
              { key: 'humidity', label: 'Humidity', value: humidity[idx], suffix: '%', scale: 'hidden' },
              { key: 'temperature_plot', label: 'Temp', value: tempPlot[idx], displayValue: tempReal[idx], suffix: ' C', scale: 'hidden' },
            ];
            let activePoint = null;
            let minDistance = Infinity;
            const pointX = u.valToPos(x[idx], 'x', true);

            candidates.forEach((candidate) => {
              if (seriesVisibility[candidate.key] === false || candidate.value == null) {
                return;
              }
              const pointY = u.valToPos(candidate.value, candidate.scale, true);
              const distance = Math.hypot(u.cursor.left - pointX, u.cursor.top - pointY);
              if (distance < minDistance) {
                minDistance = distance;
                activePoint = {
                  label: candidate.label,
                  displayValue: candidate.displayValue != null ? candidate.displayValue : candidate.value,
                  suffix: candidate.suffix,
                };
              }
            });

            if (!activePoint || minDistance > 10) {
              tooltip.hidden = true;
              tooltip.textContent = '';
              return;
            }

            tooltip.textContent = `${xLabels[idx]} | ${activePoint.label}: ${activePoint.displayValue}${activePoint.suffix}`;
            tooltip.hidden = false;
            const left = Math.max(8, Math.min(u.cursor.left + 14, plot.width - 260));
            const top = Math.max(8, u.cursor.top - 10);
            tooltip.style.left = `${left}px`;
            tooltip.style.top = `${top}px`;
          }
        ]
      }
    };

    const plot = new uPlot(opts, [x, sky, humidity, tempPlot], canvas);
    applySeriesVisibility(plot);
    weatherPlots.push(plot);
    figure.addEventListener('mouseleave', () => {
      tooltip.hidden = true;
      tooltip.textContent = '';
    });
    if (typeof ResizeObserver !== 'undefined') {
      const resizeObserver = new ResizeObserver((entries) => {
        const entry = entries[0];
        if (!entry) {
          return;
        }
        const nextWidth = Math.max(320, Math.round(entry.contentRect.width));
        if (Math.abs(nextWidth - plot.width) > 1) {
          plot.setSize({ width: nextWidth, height: plot.height });
        }
      });
      resizeObserver.observe(figure);
    }
    return panel;
  }

  async function init() {
    if (!window.uPlot) {
      setStatus('uPlot did not load.');
      return;
    }

    try {
      const response = await fetch(endpoint, { headers: { Accept: 'application/json' } });
      const payload = await response.json();
      if (!response.ok) {
        throw new Error(payload && payload.error ? payload.error : `Request failed: ${response.status}`);
      }

      const sites = Array.isArray(payload.sites) ? payload.sites : [];
      if (!sites.length) {
        setStatus('No weather plot data was returned.');
        return;
      }

      const layout = payload.layout || {};
      root.innerHTML = '';
      sites.forEach((site) => {
        root.appendChild(createCard(site, layout));
      });
    } catch (error) {
      setStatus(`Unable to load weather plot data: ${error.message}`);
    }
  }

  init();
})();
</script>
