<?php
/* test.php — visit http://localhost/job-portal/public/test.php
   DELETE THIS FILE before going live! */
$root = dirname(__DIR__);
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$base = 'http://' . $host . rtrim(dirname(dirname($_SERVER['SCRIPT_NAME']??'/')),'/') . '/public';
$ok = []; $fail = []; $warn = [];

// PHP version
version_compare(PHP_VERSION,'8.0','>=') ? $ok[]='PHP '.PHP_VERSION.' ✓' : $fail[]='PHP '.PHP_VERSION.' — need 8.0+';

// Extensions
foreach(['pdo','pdo_mysql','mbstring','json','session','fileinfo'] as $e)
    extension_loaded($e) ? $ok[]="ext/$e ✓" : $fail[]="ext/$e MISSING";

// mod_rewrite
if(function_exists('apache_get_modules'))
    in_array('mod_rewrite',apache_get_modules()) ? $ok[]='mod_rewrite ✓' : $fail[]='mod_rewrite NOT ENABLED';
else $warn[]='mod_rewrite: cannot detect';

// .htaccess RewriteBase
$ht = file_get_contents(__DIR__.'/.htaccess');
preg_match('/RewriteBase\s+(.+)/i',$ht,$m);
$rb = isset($m[1]) ? trim($m[1]) : '';
$exp = rtrim(dirname($_SERVER['SCRIPT_NAME']??'/'),'/').'/';
$rb===$exp ? $ok[]="RewriteBase: $rb ✓" : $fail[]="RewriteBase is '$rb' but should be '$exp'";

// Core files
$files = ['core/Router.php','core/Database.php','core/Session.php','core/Validator.php',
          'core/Controller.php','core/Model.php','config/app.php','config/database.php',
          'routes.php','app/helpers/functions.php','app/controllers/AuthController.php',
          'app/controllers/JobController.php','app/models/User.php',
          'app/views/layouts/main.php','app/views/auth/login.php','database/schema.sql'];
foreach($files as $f)
    file_exists($root.'/'.$f) ? $ok[]="$f ✓" : $fail[]="$f MISSING";

// Database
$cfg = require $root.'/config/database.php';
try {
    $pdo = new PDO("mysql:host={$cfg['host']};port={$cfg['port']};dbname={$cfg['dbname']};charset=utf8mb4",
                   $cfg['username'],$cfg['password'],[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    count($tables)>=10 ? $ok[]='DB connected, '.count($tables).' tables ✓'
                       : (count($tables)>0 ? $warn[]='DB: only '.count($tables).' tables — import schema.sql'
                                           : $fail[]='DB: 0 tables — import schema.sql!');
    $users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $users>0 ? $ok[]="$users demo users ✓" : $warn[]='No users — import schema.sql for demo accounts';
} catch(Exception $e) { $fail[]='DB FAILED: '.$e->getMessage(); }

// Output
header('Content-Type: text/html; charset=utf-8');
?><!DOCTYPE html><html><head><meta charset="UTF-8"><title>Portal Diagnostic</title>
<style>*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Segoe UI',sans-serif;background:#f8fafc;padding:2rem;}
h1{color:#1A56DB;border-bottom:3px solid #1A56DB;padding-bottom:10px;margin-bottom:1.5rem;}
.section{background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:1.5rem;margin-bottom:1rem;}
h2{font-size:12px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#64748b;margin-bottom:.75rem;}
ul{list-style:none;padding:0;}li{padding:4px 0;font-size:13.5px;}
.ok{color:#057A55;}.fail{color:#dc2626;font-weight:700;}.warn{color:#d97706;}
.result{border-radius:10px;padding:1.5rem;margin-top:1rem;font-size:14px;font-weight:600;}
.all-ok{background:#f0fdf4;border:1px solid #86efac;color:#166534;}
.has-fail{background:#fef2f2;border:1px solid #fecaca;color:#991b1b;}
a.go{display:inline-block;margin-top:.75rem;background:#1A56DB;color:#fff;
     padding:10px 24px;border-radius:8px;text-decoration:none;font-size:14px;}
</style></head><body>
<h1>⚙️ Job Portal — Diagnostic</h1>
<p style="color:#64748b;font-size:13px;margin-bottom:1.5rem;">
  Suggested BASE_URL: <code style="background:#f1f5f9;padding:2px 8px;border-radius:4px;color:#1A56DB;font-weight:700;"><?= $base ?></code>
</p>
<?php if($ok): ?><div class="section"><h2>✅ Passing (<?= count($ok) ?>)</h2><ul><?php foreach($ok as $i) echo "<li class='ok'>$i</li>"; ?></ul></div><?php endif; ?>
<?php if($fail): ?><div class="section"><h2>❌ Failed (<?= count($fail) ?>)</h2><ul><?php foreach($fail as $i) echo "<li class='fail'>✗ $i</li>"; ?></ul></div><?php endif; ?>
<?php if($warn): ?><div class="section"><h2>⚠️ Warnings (<?= count($warn) ?>)</h2><ul><?php foreach($warn as $i) echo "<li class='warn'>⚠ $i</li>"; ?></ul></div><?php endif; ?>

<div class="result <?= empty($fail) ? 'all-ok' : 'has-fail' ?>">
<?php if(empty($fail)): ?>
  ✅ All checks passed! Your portal is ready.<br>
  <a class="go" href="<?= $base ?>">Open the Portal →</a>
<?php else: ?>
  ❌ <?= count($fail) ?> issue(s) to fix. See the red items above.<br>
  Most common fix: enable mod_rewrite + AllowOverride All in httpd.conf, then restart Apache.
<?php endif; ?>
</div>
<p style="margin-top:1.5rem;font-size:12px;color:#94a3b8;">⚠️ Delete public/test.php before going live.</p>
</body></html>
