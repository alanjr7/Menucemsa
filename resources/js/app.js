import './bootstrap';

import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import Chart from 'chart.js/auto';

window.Chart = Chart;

Alpine.plugin(collapse);
window.Alpine = Alpine;

Alpine.start();
