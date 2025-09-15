<?php
// test_aggregator.php — demo version without database for UI testing

/********** CONFIG (panel DB) **********/
$db = [
  'host' => 'localhost',
  'user' => 'localuser',
  'pass' => 'Pkloyn7yvpht!',
  'name' => 'panel'
];
$TABLE_PREFIX = 'gsp_';
$AUTO_REFRESH_SECONDS = 30; // set 0 to disable auto-refresh
/***************************************/

// Mock data for testing
$mock_machines = [
    ['machine_id' => 'runnervmf4ws1', 'hostname' => 'runnervmf4ws1', 'last_ts' => '2025-09-15 01:00:00'],
    ['machine_id' => 'gameserver01', 'hostname' => 'gameserver01.example.com', 'last_ts' => '2025-09-15 01:01:30'],
    ['machine_id' => 'gameserver02', 'hostname' => 'gameserver02.example.com', 'last_ts' => '2025-09-15 01:02:15']
];

$mock_samples = [
    'runnervmf4ws1' => ['cpu_pct' => 25.4, 'mem_used_pct' => 68.2, 'disk_used_pct' => 42.1, 'load1' => 1.2],
    'gameserver01' => ['cpu_pct' => 78.5, 'mem_used_pct' => 89.3, 'disk_used_pct' => 56.7, 'load1' => 3.4],
    'gameserver02' => ['cpu_pct' => 12.1, 'mem_used_pct' => 34.8, 'disk_used_pct' => 28.9, 'load1' => 0.8]
];

$mock_machine_detail = [
    'machine_id' => 'runnervmf4ws1',
    'ts' => '2025-09-15 01:05:00',
    'cpu_pct' => 25.4,
    'mem_used_pct' => 68.2,
    'disk_used_pct' => 42.1,
    'load1' => 1.2,
    'load5' => 1.1,
    'load15' => 0.9,
    'disk_path' => '/home',
    'disk_used_bytes' => 42949672960,
    'disk_total_bytes' => 107374182400,
    'net_iface' => 'eth0'
];

$mock_servers = [
    ['server_name' => 'minecraft_creative', 'cpu_avg' => 12.5, 'mem_avg' => 34.2, 'folder_size_bytes' => 2147483648],
    ['server_name' => 'cs2_competitive', 'cpu_avg' => 45.8, 'mem_avg' => 67.1, 'folder_size_bytes' => 8589934592],
    ['server_name' => 'tf2_casual', 'cpu_avg' => 23.1, 'mem_avg' => 28.9, 'folder_size_bytes' => 5368709120]
];

function fmt_bytes($b) {
  if ($b===null) return '—';
  $u = ['B','KB','MB','GB','TB','PB']; $i=0;
  while ($b>=1024 && $i<count($u)-1) { $b/=1024; $i++; }
  return sprintf('%.1f %s', $b, $u[$i]);
}
function pct_class($p) {
  if ($p===null) return 'bar';
  if ($p>=80) return 'bar danger';
  if ($p>=60) return 'bar warn';
  return 'bar ok';
}
function pct($v) { return $v===null ? '—' : sprintf('%.1f%%', $v); }
function num0($v) { return $v===null ? '—' : number_format($v,0); }

$machine = isset($_GET['machine']) ? trim($_GET['machine']) : '';
$windows = ['1h'=>1, '24h'=>24, '7d'=>168];

?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>GSP Stats Dashboard<?php if ($machine) { echo " – ".htmlspecialchars($machine); } ?></title>
<?php if ($AUTO_REFRESH_SECONDS>0): ?>
<meta http-equiv="refresh" content="<?= (int)$AUTO_REFRESH_SECONDS ?>">
<?php endif; ?>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
 :root { --bg:#0b0f14; --panel:#121821; --muted:#9bb0c3; --text:#e6eef6; --ok:#1f9d55; --warn:#d4a017; --danger:#d9534f; --accent:#4ea1ff; }
 body{background:var(--bg);color:var(--text);font:14px/1.45 system-ui,Segoe UI,Roboto,Helvetica,Arial,sans-serif;margin:0;padding:24px;}
 a{color:var(--accent);text-decoration:none}
 h1,h2,h3{margin:0 0 12px 0}
 .wrap{max-width:1200px;margin:0 auto}
 .topbar{display:flex;align-items:center;gap:12px;margin-bottom:16px}
 .card{background:var(--panel);border-radius:14px;padding:16px;box-shadow:0 2px 12px rgba(0,0,0,.25)}
 .grid{display:grid;grid-template-columns:repeat(12,1fr);gap:16px}
 .col-3{grid-column:span 3} .col-4{grid-column:span 4} .col-6{grid-column:span 6} .col-12{grid-column:span 12}
 .muted{color:var(--muted)}
 .pill{display:inline-block;padding:2px 8px;border-radius:999px;background:#1a2330;color:#cfe2ff;border:1px solid #2a3a52}
 .kpi{font-size:22px;font-weight:700}
 .barwrap{background:#0e141d;border-radius:10px;height:12px;overflow:hidden}
 .bar{height:100%;display:block;width:0%;transition:width .5s ease;background:var(--ok)}
 .bar.warn{background:var(--warn)} .bar.danger{background:var(--danger)}
 table{width:100%;border-collapse:separate;border-spacing:0 8px}
 thead th{font-weight:600;color:#bcd; text-align:left; padding:8px}
 tbody td{padding:8px;background:var(--panel)}
 tbody tr{border-radius:10px}
 tbody tr td:first-child{border-top-left-radius:10px;border-bottom-left-radius:10px}
 tbody tr td:last-child{border-top-right-radius:10px;border-bottom-right-radius:10px}
 .right{text-align:right}
 .small{font-size:12px}
 .row{display:flex;gap:10px;flex-wrap:wrap}
 .btn{display:inline-block;padding:8px 10px;border:1px solid #2a3a52;border-radius:10px;background:#0e141d;color:#dfeaff}
 .btn.active{background:#1a2330}
 .split{display:flex;gap:16px;flex-wrap:wrap}
 .split > div{flex:1 1 280px}
 .subtle{opacity:.8}
 .hdr{display:flex;justify-content:space-between;align-items:center;margin-bottom:10px}
 .sep{height:1px;background:#223143;margin:16px 0}
 .mono{font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace}
</style>
</head>
<body>
<div class="wrap">
  <div class="topbar">
    <h1>GSP Stats Dashboard</h1>
    <span class="pill"><?= $AUTO_REFRESH_SECONDS>0 ? "auto-refresh {$AUTO_REFRESH_SECONDS}s" : "manual refresh" ?></span>
    <a class="btn" href="?">All machines</a>
    <?php if($machine): ?>
      <a class="btn" href="?machine=<?= urlencode($machine) ?>">Refresh</a>
    <?php endif; ?>
    <span class="pill" style="background: #d4a017; color: #000;">DEMO MODE</span>
  </div>

<?php
if (!$machine) {
    // Show machine list
    echo '<div class="grid">';
    foreach ($mock_machines as $r) {
      $sample = isset($mock_samples[$r['machine_id']]) ? $mock_samples[$r['machine_id']] : null;
      
      echo '<div class="col-4 card">';
      echo '<div class="hdr"><h3 class="mono">'.htmlspecialchars($r['hostname']).'</h3><span class="muted">'.htmlspecialchars($r['machine_id']).'</span></div>';
      echo '<div class="small muted">Last sample: '.htmlspecialchars($r['last_ts'] ?: '—').'</div>';
      
      // Show current resource usage if available
      if ($sample) {
        echo '<div class="row" style="margin-top:8px;gap:8px;">';
        echo '<div style="flex:1"><div class="small muted">CPU</div><div class="small">'.pct($sample['cpu_pct']).'</div></div>';
        echo '<div style="flex:1"><div class="small muted">MEM</div><div class="small">'.pct($sample['mem_used_pct']).'</div></div>';
        echo '<div style="flex:1"><div class="small muted">DISK</div><div class="small">'.pct($sample['disk_used_pct']).'</div></div>';
        if ($sample['load1'] !== null) {
          echo '<div style="flex:1"><div class="small muted">LOAD</div><div class="small">'.number_format((float)$sample['load1'], 1).'</div></div>';
        }
        echo '</div>';
      }
      
      echo '<div style="margin-top:10px"><a class="btn" href="?machine='.urlencode($r['machine_id']).'">Open</a></div>';
      echo '</div>';
    }
    echo '</div>';
    echo '</div></body></html>'; exit;
}

// Show individual machine details
$last = $mock_machine_detail;
?>

  <div class="grid">
    <div class="col-12 card">
      <div class="hdr">
        <h2><?= htmlspecialchars($machine) ?></h2>
        <div class="muted small">Machine ID: <?= htmlspecialchars($machine) ?> • Last sample: <?= htmlspecialchars($last['ts'] ?? '—') ?> • IF: <?= htmlspecialchars($last['net_iface'] ?? '—') ?></div>
      </div>
      <div class="split">
        <div>
          <div class="muted small">CPU (last)</div>
          <div class="kpi"><?= pct($last ? (float)$last['cpu_pct'] : null) ?></div>
          <div class="barwrap"><span class="<?= pct_class($last ? (float)$last['cpu_pct'] : null) ?>" style="width:<?= $last? min(100,max(0,(float)$last['cpu_pct'])):0 ?>%"></span></div>
        </div>
        <div>
          <div class="muted small">Memory used (last)</div>
          <div class="kpi"><?= pct($last ? (float)$last['mem_used_pct'] : null) ?></div>
          <div class="barwrap"><span class="<?= pct_class($last ? (float)$last['mem_used_pct'] : null) ?>" style="width:<?= $last? min(100,max(0,(float)$last['mem_used_pct'])):0 ?>%"></span></div>
        </div>
        <div>
          <div class="muted small">Disk used (last)</div>
          <div class="kpi"><?= pct($last ? (float)$last['disk_used_pct'] : null) ?></div>
          <div class="barwrap"><span class="<?= pct_class($last ? (float)$last['disk_used_pct'] : null) ?>" style="width:<?= $last? min(100,max(0,(float)$last['disk_used_pct'])):0 ?>%"></span></div>
          <div class="small subtle mono"><?= htmlspecialchars($last['disk_path'] ?? '') ?> • used <?= fmt_bytes($last['disk_used_bytes'] ?? null) ?> / <?= fmt_bytes($last['disk_total_bytes'] ?? null) ?></div>
        </div>
        <div>
          <div class="muted small">Load average (last)</div>
          <?php $load1 = $last ? (float)$last['load1'] : null; ?>
          <div class="kpi"><?= $load1 !== null ? number_format($load1, 1) : '—' ?></div>
          <div class="small subtle mono">1min: <?= $load1 !== null ? number_format($load1, 1) : '—' ?> • 5min: <?= $last && $last['load5'] !== null ? number_format((float)$last['load5'], 1) : '—' ?> • 15min: <?= $last && $last['load15'] !== null ? number_format((float)$last['load15'], 1) : '—' ?></div>
        </div>
        <div>
          <div class="muted small">Net avg util (1h)</div>
          <div class="kpi">2.3%</div>
          <div class="barwrap"><span class="bar ok" style="width:2.3%"></span></div>
          <div class="small subtle mono">rx 1.2 MB/s • tx 856.0 KB/s</div>
        </div>
      </div>
    </div>

    <div class="col-12 card">
      <div class="hdr">
        <h3>Game Servers - Window: 1h</h3>
        <div class="row">
          <span class="small muted">AVG CPU</span>
          <div class="barwrap" style="width:160px;"><span class="bar ok" style="width:27%"></span></div>
          <span class="small">27.1%</span>
          <span class="small muted" style="margin-left:14px;">AVG MEM</span>
          <div class="barwrap" style="width:160px;"><span class="bar warn" style="width:68%"></span></div>
          <span class="small">68.2%</span>
        </div>
      </div>

      <div class="sep"></div>

      <div class="tablewrap">
        <table>
          <thead>
            <tr>
              <th>Server</th>
              <th class="right">CPU avg</th>
              <th style="width:220px"></th>
              <th class="right">Mem avg</th>
              <th style="width:220px"></th>
              <th class="right">Folder size</th>
            </tr>
          </thead>
          <tbody>
            <?php
            foreach ($mock_servers as $s) {
              $cpu = $s['cpu_avg']!==null ? (float)$s['cpu_avg'] : null;
              $mem = $s['mem_avg']!==null ? (float)$s['mem_avg'] : null;
              echo '<tr>';
              echo '<td class="mono">'.htmlspecialchars($s['server_name']).'</td>';
              echo '<td class="right">'.pct($cpu).'</td>';
              echo '<td><div class="barwrap"><span class="'.pct_class($cpu).'" style="width:'.($cpu!==null?min(100,max(0,$cpu)):0).'%"></span></div></td>';
              echo '<td class="right">'.pct($mem).'</td>';
              echo '<td><div class="barwrap"><span class="'.pct_class($mem).'" style="width:'.($mem!==null?min(100,max(0,$mem)):0).'%"></span></div></td>';
              echo '<td class="right">'.fmt_bytes($s['folder_size_bytes']).'</td>';
              echo '</tr>';
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>
</body>
</html>