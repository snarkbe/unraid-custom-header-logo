<?php
/* Custom Header Logo - serve the logo stored on the flash drive.
 * Drop logo.svg / logo.png / logo.webp / logo.jpg / logo.gif into
 * /boot/config/plugins/custom.header.logo/ : it survives reboots and plugin updates.
 */
$types = ['svg' => 'image/svg+xml', 'png' => 'image/png', 'webp' => 'image/webp',
          'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'gif' => 'image/gif'];
$dir = '/boot/config/plugins/custom.header.logo';
foreach ($types as $ext => $mime) {
  $file = "$dir/logo.$ext";
  if (!is_file($file)) continue;
  $mtime = filemtime($file);
  header("Content-Type: $mime");
  header('X-Content-Type-Options: nosniff');
  // An SVG opened directly must not be able to run scripts in the webGUI origin.
  header("Content-Security-Policy: default-src 'none'; style-src 'unsafe-inline'");
  // The hook appends ?v=<mtime>, so a changed file gets a new URL.
  header('Cache-Control: private, max-age=86400');
  header('Last-Modified: '.gmdate('D, d M Y H:i:s', $mtime).' GMT');
  header('Content-Length: '.filesize($file));
  readfile($file);
  exit;
}
http_response_code(404);
