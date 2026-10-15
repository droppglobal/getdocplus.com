<?php

$config['protocol'] = 'sendmail';
$config['mailpath'] = '/usr/sbin/sendmail';
$config['charset'] = 'utf-8';
$config['wordwrap'] = TRUE;
$config['mailtype'] = 'html'; 
$config['from_email'] = '';
$config['from_name'] = '';

if($config['protocol']=="smtp"){
$config['smtp_host'] = '';
$config['smtp_user'] = '';
$config['smtp_pass'] = '';
$config['smtp_port'] = '';
$config['smtp_crypto'] = 'ssl';

}
$config['smtp_timeout'] = 60;
