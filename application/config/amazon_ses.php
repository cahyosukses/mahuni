<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Config for the Amazon Simple Email Service library
 *
 * @see ../libraries/Amazon_ses.php
 */
// Amazon credentials
$config['amazon_ses_secret_key'] = '3L+dtYWRqlh/AbbjaWzOjTr7LuWc4H0Z6O2NhoRQ';
$config['amazon_ses_access_key'] = 'AKIAI5EZHPYHWQ4WLF6A';

// Adresses
$config['amazon_ses_from'] = 'webmaster@yezza.net';
$config['amazon_ses_from_name'] = 'YEZZA';
$config['amazon_ses_reply_to'] = 'webmaster@yezza.net';

// Path to certificate to verify SSL connection (i.e. 'certs/cacert.pem') 
$config['amazon_ses_cert_path'] = 'assets/certs/cacert.pem';

// Charset to be used, for example UTF-8, ISO-8859-1 or Shift_JIS. The SMTP
// protocol uses 7-bit ASCII by default
$config['amazon_ses_charset'] = '';
