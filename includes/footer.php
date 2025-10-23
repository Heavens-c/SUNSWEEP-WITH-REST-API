</main>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- App JS: Sidebar + Battery Chart -->
<script>
// === SIDEBAR TOGGLE (for mobile) ===
const menuBtn = document.querySelector('.menu-btn');
const sidebar = document.querySelector('.sidebar');
const overlay = document.createElement('div');
overlay.className = 'overlay';
document.body.appendChild(overlay);

menuBtn?.addEventListener('click', () => {
  sidebar.classList.toggle('active');
  overlay.classList.toggle('show');
});

overlay.addEventListener('click', () => {
  sidebar.classList.remove('active');
  overlay.classList.remove('show');
});

// === FETCH BATTERY CHART DATA FROM DATABASE ===
const ctx = document.getElementById('mainChart');
if (ctx) {
  const css = (v) => getComputedStyle(document.documentElement).getPropertyValue(v).trim();

  fetch('/sunsweep/api/charts_data.php')
    .then(res => res.json())
    .then(data => {
      new Chart(ctx, {
        type: 'bar',
        data: {
          labels: data.labels,
          datasets: [{
            label: 'Battery Level (%)',
            data: data.battery,
            backgroundColor: css('--accent'),
            borderRadius: 6,
            barThickness: 24
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false },
            tooltip: {
              backgroundColor: css('--panel'),
              titleColor: css('--text'),
              bodyColor: css('--text'),
              callbacks: { label: (ctx) => `Battery: ${ctx.parsed.y}%` }
            }
          },
          animation: { duration: 1500, easing: 'easeOutQuart' },
          scales: {
            x: {
              ticks: { color: css('--text'), font: { size: 11 } },
              grid: { display: false }
            },
            y: {
              beginAtZero: true,
              max: 100,
              ticks: {
                color: css('--text'),
                stepSize: 20,
                callback: (v) => v + '%'
              },
              grid: { color: css('--muted') }
            }
          }
        }
      });
    })
    .catch(err => console.error('Chart fetch error:', err));
}
</script>

<style>
/* === Overlay for mobile sidebar === */
.overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(2px);
  display: none;
  z-index: 90;
}
.overlay.show {
  display: block;
}

/* Chart container styling */
#mainChart {
  width: 100% !important;
  height: 320px !important;
  background: var(--panel);
  border-radius: 14px;
  padding: 12px;
  box-sizing: border-box;
}
</style>

</body>
</html>
