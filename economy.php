<!DOCTYPE html>
<html>
<?php include "head.php"; ?>
<body>
<?php
require 'menu.php';
$economy_plot_endpoint = 'economy_plot_data.php';
?>
<link rel="stylesheet" href="https://unpkg.com/uplot@1.6.31/dist/uPlot.min.css">
<section class="panel">
  <div class="weather-plot-legend">
    <span class="weather-plot-legend__item"><span class="weather-plot-legend__dot weather-plot-legend__dot--treasury-3mo"></span>3Mo</span>
    <span class="weather-plot-legend__item"><span class="weather-plot-legend__dot weather-plot-legend__dot--treasury-1yr"></span>1Yr</span>
    <span class="weather-plot-legend__item"><span class="weather-plot-legend__dot weather-plot-legend__dot--treasury-2yr"></span>2Yr</span>
    <span class="weather-plot-legend__item"><span class="weather-plot-legend__dot weather-plot-legend__dot--treasury-5yr"></span>5Yr</span>
    <span class="weather-plot-legend__item"><span class="weather-plot-legend__dot weather-plot-legend__dot--treasury-10yr"></span>10Yr</span>
    <span class="weather-plot-legend__item"><span class="weather-plot-legend__dot weather-plot-legend__dot--treasury-30yr"></span>30Yr</span>
  </div>
</section>
<section class="panel">
  <div id="economy-plot-root" class="weather-plot-root" data-endpoint="<?php echo htmlspecialchars($economy_plot_endpoint, ENT_QUOTES, 'UTF-8'); ?>">
    <p class="weather-plot-status">Loading economy plot...</p>
  </div>
</section>
<script src="https://unpkg.com/uplot@1.6.31/dist/uPlot.iife.min.js"></script>
<script>
(() => {
  const root = document.getElementById('economy-plot-root');
  if (!root) {
    return;
  }

  const endpoint = root.dataset.endpoint || '';
  const SERIES_STYLES = {
    '3Mo': { stroke: 'purple', fill: 'purple' },
    '1Yr': { stroke: 'firebrick', fill: 'firebrick' },
    '2Yr': { stroke: 'green', fill: 'green' },
    '5Yr': { stroke: 'orange', fill: 'orange' },
    '10Yr': { stroke: 'blue', fill: 'blue' },
    '30Yr': { stroke: 'yellow', fill: 'yellow' }
  };

  function setStatus(message) {
    root.innerHTML = `<p class="weather-plot-status">${message}</p>`;
  }

  function pointSeries(label, style) {
    return {
      label,
      stroke: style.stroke,
      width: 0,
      paths: () => null,
      points: {
        show: true,
        size: 3,
        stroke: style.stroke,
        fill: style.fill,
        width: 1
      }
    };
  }

  function computeLabelIndexes(x, width) {
    const indexes = [];
    const targetLabelCount = Math.max(6, Math.floor(width / 90));
    const interval = Math.max(1, Math.ceil(x.length / targetLabelCount));
    x.forEach((value, idx) => {
      if (idx % interval === 0 || idx === x.length - 1) {
        indexes.push(idx);
      }
    });
    return indexes;
  }

  function createChart(chart, layout) {
    const panel = document.createElement('section');
    panel.className = 'panel';

    const header = document.createElement('div');
    header.className = 'weather-card__header weather-card__header--compact';
    const title = document.createElement('h2');
    title.className = 'weather-card__title weather-card__title--compact';
    title.textContent = chart.title || 'Treasury Rate';
    const meta = document.createElement('div');
    meta.className = 'weather-card__meta weather-card__meta--compact';
    meta.textContent = chart.meta || '';
    header.appendChild(title);
    header.appendChild(meta);
    panel.appendChild(header);

    const figure = document.createElement('figure');
    figure.className = 'media-panel image-scroll weather-plot-frame';
    const canvas = document.createElement('div');
    canvas.className = 'weather-plot-canvas weather-plot-canvas--full';
    figure.appendChild(canvas);
    panel.appendChild(figure);

    const x = Array.isArray(chart.x) ? chart.x : [];
    const xLabels = Array.isArray(chart.x_labels) ? chart.x_labels : [];
    const names = ['3Mo', '1Yr', '2Yr', '5Yr', '10Yr', '30Yr'];
    const series = names.map((name) => Array.isArray(chart.series && chart.series[name]) ? chart.series[name] : []);
    const softText = getComputedStyle(document.documentElement).getPropertyValue('--text-soft').trim() || '#b3c2cc';

    const opts = {
      width: Number(layout.width) || 873,
      height: Math.max(320, Number(layout.height) || 290),
      cursor: { drag: { x: true, y: false } },
      scales: {
        x: { time: false, range: [Math.min(...x), Math.max(...x)] },
        y: { range: [Number(layout.y_min), Number(layout.y_max)] }
      },
      series: [{}, ...names.map((name) => pointSeries(name, SERIES_STYLES[name]))],
      axes: [
        {
          stroke: 'rgba(179, 194, 204, 0.88)',
          grid: { show: false },
          values: () => [],
          size: 82
        },
        {
          stroke: 'rgba(179, 194, 204, 0.88)',
          grid: { show: true, stroke: 'rgba(179, 194, 204, 0.88)', width: 1, dash: [2, 4] },
          values: (self, splits) => splits.map((value) => value.toFixed(2))
        }
      ],
      hooks: {
        drawAxes: [
          (u) => {
            const ctx = u.ctx;
            ctx.save();
            ctx.strokeStyle = 'rgba(179, 194, 204, 0.38)';
            ctx.lineWidth = 1;
            ctx.fillStyle = softText;
            ctx.font = '12px sans-serif';
            ctx.textBaseline = 'middle';
            const top = u.bbox.top;
            const bottom = u.bbox.top + u.bbox.height;
            const labelIndexes = computeLabelIndexes(x, u.bbox.width);
            labelIndexes.forEach((idx) => {
              const value = x[idx];
              const xPos = Math.round(u.valToPos(value, 'x', true));
              ctx.beginPath();
              ctx.moveTo(xPos, top);
              ctx.lineTo(xPos, bottom);
              ctx.setLineDash([2, 4]);
              ctx.stroke();
              ctx.setLineDash([]);
              const label = xLabels[idx] || '';
              ctx.save();
              ctx.translate(xPos + 4, bottom + 4);
              ctx.rotate(-Math.PI / 2);
              ctx.textAlign = 'right';
              ctx.fillText(label, 0, 0);
              ctx.restore();
            });
            ctx.restore();
          }
        ],
        setCursor: [
          (u) => {
            const idx = u.cursor.idx;
            if (idx == null || idx < 0 || idx >= xLabels.length) {
              canvas.removeAttribute('title');
              return;
            }
            const parts = [xLabels[idx]];
            names.forEach((name, seriesIndex) => {
              const value = series[seriesIndex][idx];
              if (value != null) {
                parts.push(`${name}: ${Number(value).toFixed(2)}`);
              }
            });
            canvas.title = parts.join(' | ');
          }
        ]
      }
    };

    new uPlot(opts, [x, ...series], canvas);
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
      root.innerHTML = '';
      root.appendChild(createChart(payload.chart || {}, payload.layout || {}));
    } catch (error) {
      setStatus(`Unable to load economy plot: ${error.message}`);
    }
  }

  init();
})();
</script>
<?php
include 'tail.php';
?>
</body>
</html>
