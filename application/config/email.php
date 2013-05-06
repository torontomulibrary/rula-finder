<?php
/**
  *  Email Config Variables
  *
  *  These come directly from the documentation of Code Igniter 1.7.2 website.
  *  http://codeigniter.com/user_guide/libraries/email.html
  *
  *  Var                Default             Options                     Description
  *  useragent            CodeIgniter        None                    The "user agent".
  *  protocol            mail                mail, sendmail, or smtp    The mail sending protocol.
  *  mailpath            /usr/sbin/sendmail  None                    The server path to Sendmail.
  *  smtp_host            No Default        None                    SMTP Server Address.
  *  smtp_user            No Default        None                    SMTP Username.
  *  smtp_pass            No Default        None                    SMTP Password.
  *  smtp_port            25                None                    SMTP Port.
  *  smtp_timeout       5                None                    SMTP Timeout (in seconds).
  *  wordwrap            TRUE                TRUE or FALSE (boolean)    Enable word-wrap.
  *  wrapchars            76                                        Character count to wrap at.
  *  mailtype            text                text or html            Type of mail. If you send HTML email you must send it as a complete web page. Make sure you don't have any relative links or relative image paths otherwise they will not work.
  *  charset            utf-8                                        Character set (utf-8, iso-8859-1, etc.).
  *  validate            FALSE                TRUE or FALSE (boolean)    Whether to validate the email address.
  *  priority            3                1, 2, 3, 4, 5            Email Priority. 1 = highest. 5 = lowest. 3 = normal.
  *  crlf             \n                 "\r\n" or "\n" or "\r"     Newline character. (Use "\r\n" to comply with RFC 822).
  *  newline            \n                 "\r\n" or "\n" or "\r"    Newline character. (Use "\r\n" to comply with RFC 822).
  *  bcc_batch_mode    FALSE                TRUE or FALSE (boolean)    Enable BCC Batch Mode.
  *  bcc_batch_size    200                None                    Number of emails in each BCC batch.
  *  
  */                       
$config['useragent']        = 'CodeIgniter';        
$config['protocol']         = 'smtp';        
$config['mailpath']         = '';
$config['smtp_host']        = 'mail.server.com';	//Change me!
$config['smtp_user']        = '';
$config['smtp_pass']        = '';
$config['smtp_port']        = 25;
$config['smtp_timeout']     = 5;
$config['wordwrap']         = TRUE;
$config['wrapchars']        = 76;
$config['mailtype']         = 'html';
$config['charset']          = 'utf-8';
$config['validate']         = FALSE;
$config['priority']         = 3;
$config['crlf']             = "\r\n";
$config['newline']          = "\r\n";
$config['bcc_batch_mode']   = FALSE;
$config['bcc_batch_size']   = 200;
