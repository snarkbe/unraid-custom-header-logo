<?php
/* Custom Header Logo - upload or delete the logo stored on the flash drive.
 * POST cmd=upload (multipart field "logo") or cmd=delete.
 * CSRF: the webGUI's auto-prepended local_prepend.php rejects every POST without a
 * valid csrf_token, then strips the token from $_POST and $_SERVER, so it cannot be
 * re-checked here.
 */
header('Content-Type: application/json');

function chl_reply($code, $data) {
  http_response_code($code);
  exit(json_encode($data));
}

$dir   = '/boot/config/plugins/custom.header.logo';
$exts  = ['svg', 'png', 'webp', 'jpg', 'jpeg', 'gif'];
$limit = 512 * 1024;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') chl_reply(405, ['error' => 'POST required']);

function chl_clear($dir, $exts) {
  foreach ($exts as $e) @unlink("$dir/logo.$e");
}

switch ($_POST['cmd'] ?? '') {
case 'delete':
  chl_clear($dir, $exts);
  chl_reply(200, ['ok' => true]);

case 'upload':
  $f = $_FILES['logo'] ?? null;
  if (!$f || !is_array($f) || ($f['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || !is_uploaded_file($f['tmp_name'])) {
    chl_reply(400, ['error' => 'Upload failed']);
  }
  if ($f['size'] > $limit) chl_reply(413, ['error' => 'File too large (max 512 KB)']);

  // Detect the real type from the content, never from the name or the browser.
  $ext = null;
  $info = @getimagesize($f['tmp_name']);
  if ($info) {
    $ext = [IMAGETYPE_PNG => 'png', IMAGETYPE_JPEG => 'jpg', IMAGETYPE_GIF => 'gif',
            IMAGETYPE_WEBP => 'webp'][$info[2]] ?? null;
  } else {
    $svg = file_get_contents($f['tmp_name']);
    // No DTD (entity expansion), no scripts: an SVG logo needs neither.
    if ($svg !== false && !preg_match('/<!DOCTYPE|<!ENTITY|<script|\bon\w+\s*=|javascript:/i', $svg)) {
      $xml = @simplexml_load_string($svg, 'SimpleXMLElement', LIBXML_NONET);
      if ($xml !== false && strtolower($xml->getName()) === 'svg') $ext = 'svg';
    }
  }
  if (!$ext) chl_reply(415, ['error' => 'Unsupported file: use PNG, SVG, WebP, JPG or GIF']);

  if (!is_dir($dir)) @mkdir($dir, 0777, true);
  chl_clear($dir, $exts);
  $dest = "$dir/logo.$ext";
  if (!move_uploaded_file($f['tmp_name'], $dest)) chl_reply(500, ['error' => 'Could not write to the flash drive']);
  chl_reply(200, ['ok' => true, 'file' => "logo.$ext", 'v' => filemtime($dest)]);

default:
  chl_reply(400, ['error' => 'Unknown command']);
}
