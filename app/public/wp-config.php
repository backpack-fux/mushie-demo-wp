<?php

# Database Configuration
define('DB_NAME', 'local');
define('DB_USER', 'root');
define('DB_PASSWORD', 'root');
define('DB_HOST', 'localhost');
define('DB_HOST_SLAVE', '127.0.0.1:3306');
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', 'utf8_unicode_ci');
$table_prefix = 'wp_';

# Security Salts, Keys, Etc
define('AUTH_KEY', '[-k9-lKRt+j,M~M--T>r{Zuj/?Hy=eTa3T$4Q#O=?8<n)Yez:MY)Z.$1a+l}6.!Z');
define('SECURE_AUTH_KEY', 'dcu_h[s*^*$f]r|tb0?/X]ZA[FT>*@CC1E|s;i)!rd1-s>Ok6$0@Bk[wAcqm@chC');
define('LOGGED_IN_KEY', 'm4CE/^C`o94InF}f~O&%hK40a,Eal<Yz0ew$JEg,ct&0J#G[x/<rq)n||7p}3KpP');
define('NONCE_KEY', '|OBgsKQV1|-6e-o$&9$}Fj`fnS[k-7rCQQ/R*|Z]KGb/nY0a[bCOK8*Y/~_-r]^U');
define('AUTH_SALT', 'ZAo^x_r-9dUJ!dl3[4i7{avt-[+W#FB{=FzA0vD>LNo~3hJ5QY:m+INRU+a,AQT4');
define('SECURE_AUTH_SALT', 'h,QpXEY1pbYAzS!s+tmZI}edmF@N~;4A1+?Ep|71R_AvSN_,_TdC([7,S@ov-fie');
define('LOGGED_IN_SALT', 'S]?-t%Cb+.vK}4rAB**K)[jY=Q=t_tczFc[x!s-_<Xa/n}ls&UTJg~E+loA}j>Uw');
define('NONCE_SALT', 'dj0<j*jS$tuYT^9f.D2y+XT#qh{X,E_pw*kG9OeU^$<W&2|yo<s#5;?k6T fjrBJ');


# Localized Language Stuff

define('WP_CACHE', true);

define('WP_AUTO_UPDATE_CORE', false);

define('PWP_NAME', 'mushieschocol1');

define('FS_METHOD', 'direct');

define('FS_CHMOD_DIR', 0775);

define('FS_CHMOD_FILE', 0664);

define('WPE_APIKEY', '9157b1e1d47e7a97d73b997cfd96cc5885d5df0b');

define('WPE_CLUSTER_ID', '141351');

define('WPE_CLUSTER_TYPE', 'pod');

define('WPE_ISP', true);

define('WPE_BPOD', false);

define('WPE_RO_FILESYSTEM', false);

define('WPE_LARGEFS_BUCKET', 'largefs.wpengine');

define('WPE_SFTP_PORT', 2222);

define('WPE_SFTP_ENDPOINT', '');

define('WPE_LBMASTER_IP', '');

define('WPE_CDN_DISABLE_ALLOWED', true);

define('DISALLOW_FILE_MODS', false);

define('DISALLOW_FILE_EDIT', false);

define('DISABLE_WP_CRON', false);

define('WPE_FORCE_SSL_LOGIN', true);

define('FORCE_SSL_LOGIN', true);

/*SSLSTART*/ if (isset($_SERVER['HTTP_X_WPE_SSL']) && $_SERVER['HTTP_X_WPE_SSL']) {
    $_SERVER['HTTPS'] = 'on'; /*SSLEND*/
}
define('WPE_EXTERNAL_URL', false);

define('WP_POST_REVISIONS', false);

define('WPE_WHITELABEL', 'wpengine');

define('WP_TURN_OFF_ADMIN_BAR', false);

define('WPE_BETA_TESTER', false);

umask(0002);

$wpe_cdn_uris = array ( );

$wpe_no_cdn_uris = array ( );

$wpe_content_regexs = array ( );

$wpe_all_domains = array ( 0 => 'demo-store-dev.wpengine.com', 1 => 'demo-store-stg.wpenginepowered.com', 2 => 'demo-store-wp.pylon.im', 3 => 'www.demo-store-wp.pylon.im', );

$wpe_varnish_servers = array ( 0 => 'pod-141351', );

$wpe_special_ips = array ( 0 => '34.170.238.85', );

$wpe_netdna_domains = array ( );

$wpe_netdna_domains_secure = array ( );

$wpe_netdna_push_domains = array ( );

$wpe_domain_mappings = array ( );

$memcached_servers = array ( 'default' =>  array ( 0 => 'unix:///tmp/memcached.sock', ), );
define('WPLANG', '');

# WP Engine ID


# WP Engine Settings






# That's It. Pencils down
if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}
require_once(ABSPATH . 'wp-settings.php');
