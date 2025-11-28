<?php
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

define('DB_HOST','localhost');
define('DB_USER','root');
define('DB_PASS','');
define('DB_NAME','animenes_solaz'); 


define('BASE_URL','http://localhost/Tugas_Akhir_PAW');
