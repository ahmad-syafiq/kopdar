#!/bin/bash

set -e

# buat folder jika belum ada
BASE_DIR="../.."
MOD_DIR="$BASE_DIR/modules/dashboard"
TPL_DIR="$BASE_DIR/templates/dashboard"
IMG_DIR="$BASE_DIR/images"

mkdir -p "$MOD_DIR"
CONFIG_FILE="$MOD_DIR/_config.php"
SWITCH_FILE="$MOD_DIR/_switch.php"

for f in "$CONFIG_FILE" "$SWITCH_FILE"; do
  if [ -f "$f" ]; then
    echo "✗ File sudah ada: $f"
    exit 1
  fi
done



# import database tambahan
read -p "Masukkan nama database: " DB_NAME
if [ -z "$DB_NAME" ]; then
  echo "✗ Nama database tidak boleh kosong"
  exit 1
fi

read -s -p "Masukkan password MySQL: " DB_PASS
echo
if [ -z "$DB_PASS" ]; then
  echo "✗ Password tidak boleh kosong"
  exit 1
fi

mysql -u root -p"$DB_PASS" "$DB_NAME" < dashboard.sql

echo "✓ Import dashboard.sql ke database '$DB_NAME' berhasil"



# tulis file config
cat <<'EOF' > "$CONFIG_FILE"
<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

// jika dijalankan di http://localhost
if (_URI != '/' && !empty($_GET['return'])) {
  $_GET['return'] = preg_replace('~'.trim(_URI, '/').'(?:%2F|/)~i', '', $_GET['return']);
}
EOF

echo "✓ _config.php berhasil dibuat di $CONFIG_FILE"



# tulis file switch
cat <<'EOF' > "$SWITCH_FILE"
<?php if (!defined('_VALID_BBC')) exit('No direct script access allowed');

$_CONFIG['template'] = 'dashboard';
$sys->layout_change('dashboard');

switch ($Bbc->mod['task'])
{
	case 'main': // Untuk menampilkan halaman utama dashboard
		$sys->nav_change('<i class="fa fa-dashboard"></i> Dashboard', site_url('dashboard'));
		
		pr($user, __FILE__.':'.__LINE__);
		pr($sys, __FILE__.':'.__LINE__);
		pr($Bbc, __FILE__.':'.__LINE__);
		pr($Bbc->mod['task'], __FILE__.':'.__LINE__);

		break;


	case 'list': // Untuk menampilkan list
		pr('list', __FILE__.':'.__LINE__);
		break;



	case 'logout':
		redirect('user/logout');
		break;

	default:
		echo 'Invalid action <b>'.$Bbc->mod['task'].'</b> has been received...';
		break;
}

EOF

echo "✓ _switch.php berhasil dibuat di $SWITCH_FILE"



# clear cache
rm -rf $IMG_DIR/cache
echo "✓ Cache dibersihkan"

