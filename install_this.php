<?php

// Not many errors show, as 1 si set
error_reporting(-1);

include_once('functions/func.php');

function mysql_dead($str)
{
	$s = '';
	$s .= '<br />';
	$s .=  $str;
	$s .= '<br />';
	$s .= 'Errorno: ' . mysql_errno(); 
	$s .= '<br />';
	$s .= 'Error: ' . mysql_error();
	$s .= '<br />';
	echo $s;
	die();
}

if(!isset($_GET['step']))
{
	echo '
	<input type="button" value="Start Install" onClick="javascript:location.href=\'install.php?step=1\'">
	';
}

//echo "<br />";

if(isset($_POST['dbinstall']))
{
	$host = mandff($_POST['host'], 'Invalid Host');
	$dbuser = mandff($_POST['dbuser'], 'Invalid DB User');
	$dbpass = optff($_POST['dbpass']);
	$dbname = mandff($_POST['dbname'], 'Invalid Database');
	
	if(!empty($error))
	{
		echo "Errors: <br />";
		foreach($error as $err)
			echo $err . "<br />";
		die();
	}
	
	$dbname = un_sql_inj($dbname);
	
	$dbconn = mysql_connect($host, $dbuser, $dbpass) or mysql_dead('Could not establish connection to the Database.');
	$select_db = mysql_select_db($dbname, $dbconn) or mysql_dead('Could not select DB, please check if DB exists.');
	
	if(!empty($error))
	{
		echo "Errors: <br />";
		foreach($error as $err)
			echo $err . "<br />";
		die();
		//die("Errors: " . $error);
	}
	
	
	
	
	
	$dirName = dirname(__FILE__);
	
	// if directory is_writable only then move forward, 
	// else give an error and give a link to go back and start installation from this page itself.
	if(!is_writable($dirName) )
	{
		$error['dir_unwritable'] = 'Directory "' . $dirName . '" does not seem to be writable, please try changing permissions, and submit the form again.';
	}
	
	if(!empty($error))
	{
		echo "Errors: <br />";
		foreach($error as $err)
			echo $err . "<br />";
		die();
		//die("Errors: " . $error);
	}
	
	
	$fh = @fopen('config.php', "w+");
	$str = '<?php ' . "\n" .
	'$globals["host"] = $host = \'' . $host. "'; \n" . 
	'$globals["dbuser"] = $dbuser = \'' . $dbuser . "'; \n" . 
	'$globals["dbpass"] = $dbpass = \'' . $dbpass . "'; \n" . 
	'$globals["dbname"] = $dbname = \'' . $dbname . "'; \n" . 
	'$globals["rootdir"] = $rootdir = \'' . $dirName . "'; \n" . 
	'$globals["boarddir"] = $boarddir = \'' . $dirName . "'; \n" . 
	'$globals["cachedir"] = $cachedir = \'' . $dirName . "/cache" . "'; \n" . 
	'$globals["sourcedir"] = $sourcedir = \'' . $dirName . "/sources" . "'; \n" . 
	'$globals["themedir"] = $themedir = \'' . $dirName . "/themes" . "'; \n" . 
	// '$globals["boardurl"] = $boardurl = \'http://' . $host . dirname($_SERVER['REQUEST_URI']) . "'; \n" . 
	// '$globals["boardurl"] = $boardurl = \'http://' . $host . dirname($_SERVER['SCRIPT_NAME']) . "'; \n" . 
	// '$globals["boardurl"] = $boardurl = \'http://' . $_SERVER['SERVER_NAME'] . "'; \n" . 
	'$globals["boardurl"] = $boardurl = \'http://' . $host . dirname($_SERVER['SCRIPT_NAME']) . "'; \n" . 
	// '$globals["boardurl"] = $boardurl = \'http://' . $host . dirname($_SERVER['REQUEST_URI']) . "'; \n" . 
	'$globals["boardurl"] = $boardurl = \'http://' . $host . dirname($_SERVER['SCRIPT_NAME']) . "'; \n" . 
	
	'$globals["cookiename"] = $cookiename = \'MFCOOKIE44' . "'; \n" . 
	'// Will have to write the below things in this file at installation ' . "\n" . 
	'// extra added ' . "\n" .
	'$globals["ind"] = $globals["boardurl"]."/index.php?"; ' . "\n" . 
	'$globals["only_ind"] = "/index.php?"; ' . "\n\n// Added all \n\n" . 
	'$globals["funcdir"] = $funcdir = \'' . $dirName . '/functions\';' . 
	"\n" . 
	'// Only for the moment, will be removed later.' . 
	"\n" . 
	'// Extra added Admin specifications' . 
	"\n" . 
	'// Admin settings will come from settings table of admin' . 
	"\n" . 
	'$admin["settings"]["ai"] = 1;' . 
	"\n" . 
	'// Single file for Languages as in all languages function kept in single file, ' . 
	"\n" . 
	'// or multiple file for multiple functions' . 
	"\n" . 
	'//$admin["settings"]["lang"]["func_run"] = "sing";' . 
	"\n" . 
	'$admin["settings"]["lang"]["func_run"] = "mult";' . 
	"\n" . 
	'// extra added' . 
	"\n" . 
	'// DB Specifications' . 
	"\n" . 
	'$db = array(); ' . 
	"\n" . 
	'$db["type"] = "mysql";' . 
	"\n" . 
	
	"?>"
	;
	
	$fw = fwrite($fh, $str);
	
	/*
	$fh = fopen('config.php', "w+");
	$fw = fwrite($fh, "<?php \n");
	$fw = fwrite($fh, '$globals["host"] = $host = \'' . $host. "'; \n");
	$fw = fwrite($fh, '$globals["dbuser"] = $dbuser = \'' . $dbuser . "'; \n");
	$fw = fwrite($fh, '$globals["dbpass"] = $dbpass = \'' . $dbpass . "'; \n");
	$fw = fwrite($fh, '$globals["dbname"] = $dbname = \'' . $dbname . "'; \n");
	$fw = fwrite($fh, '$globals["rootdir"] = $rootdir = \'' . dirname(__FILE__) . "'; \n");
	$fw = fwrite($fh, '$globals["boarddir"] = $boarddir = \'' . dirname(__FILE__) . "'; \n");
	$fw = fwrite($fh, '$globals["cachedir"] = $cachedir = \'' . dirname(__FILE__) . "/Cache" . "'; \n");
	$fw = fwrite($fh, '$globals["sourcedir"] = $sourcedir = \'' . dirname(__FILE__) . "/Sources" . "'; \n");
	$fw = fwrite($fh, '$globals["themedir"] = $themedir = \'' . dirname(__FILE__) . "/Themes" . "'; \n");
	$fw = fwrite($fh, '$globals["boardurl"] = $boardurl = \'http://' . $host . dirname($_SERVER['REQUEST_URI']) . "'; \n");
	$fw = fwrite($fh, '$globals["cookiename"] = $cookiename = \'MFCOOKIE44' . "'; \n");
	
	// Will have to write the below things in this file at installation
	// extra added
	$globals["ind"] = $globals["boardurl"]."/index.php?";
	$globals["only_ind"] = "/index.php?";
	
	$fw = fwrite($fh, "?>");
	*/
	
	fclose($fh);
	
	
	if(file_exists('config.php'))
	{
		// Redirect to other location`
		header("Location: install.php?step=2");
		exit(0);
	}
	else
	{
//			return 'Config file does not exists';
		echo 'Config file does not exists';
		exit(1);
	}
	
}


if(isset($_GET['step']) && $_GET['step'] == 1)
{
	echo '
	<form action="" method="POST">
	Host: 
	<small>(Your hostname. On local machines, its generally: <i>localhost</i>)</small>
	<br />
	<input type="text" name="host" id="host" value="'.( isset($_POST['host']) ? $_POST['host'] : '' ).'"><br />
	DB-User: 
	<small>(Database Username)</small>
	<br />
	<input type="text" name="dbuser" id="dbuser" value="'.( isset($_POST['dbuser']) ? $_POST['dbuser'] : '' ).'">
	<br />
	DB-Pass: 
	<small>(Database Password)</small>
	<br />
	<input type="text" name="dbpass" id="dbpass" value="'.( isset($_POST['dbpass']) ? $_POST['dbpass'] : '' ).'">
	<br />
	DB-Name: 
	<small>(Database name to connect to)</small>
	<br />
	<input type="text" name="dbname" id="dbname" value="'.( isset($_POST['dbname']) ? $_POST['dbname'] : '' ).'">
	<br />
	<br />
	<input type="submit" value="Next >>" name="dbinstall">
	</form>
	';
}

// $mysql->mc('localhost', 'root', '');

// $dbname = "myforum_2";

// $mysql->mq("CREATE DATABASE IF NOT EXISTS " . $dbname);

// $mysql->mq("SOURCE myforum_3.sql");


// "SOURCE file.sql"

///////
// good one
//
// passthru("nohup mysql -u USERNAME -pPASSWORD DBNAME < dump.sql");
//////

/*
// CREATE DATABASE IF NOT EXISTS `test_MF`

//$dbname = "test_MF";
$dbname = "information_schema";
//$dbname = "muntoofo_myforum_3";

$dbconn = mysql_connect("localhost", "muntoofo", "ashposeidon!!1") or die('Could not establish mysql connection');
echo '$dbconn: ' . $dbconn . "<br />";
$seldb = mysql_select_db( $dbname) or die("Cud not select DB");
//$dbmake = mysql_query("CREATE DATABASE IF NOT EXISTS " . $dbname) or die('DB NOT CREATED');
//$dbmake = mysql_query("SHOW DATABASES;") or die('DB NOT CREATED');
$dbmake = mysql_query("show tables") or die('DB NOT CREATED: Error no: ' . mysql_errno() . " Error: " . mysql_error() );

while( $row = mysql_fetch_assoc($dbmake) )
{
	printrr( $row );
}



if( $dbmake )
	echo "dbmake";

echo 1;
exit();

printrr($_SERVER);
echo ( file_exists(dirname($_SERVER['SCRIPT_FILENAME'] ) . "/myforum_3.sql" ) ) ? "yes" : "no" ;
*/



if(isset($_POST['admin_set']))
{
		
	$adminuser = mandff($_POST['adminuser'], 'Invalid Admin Username');
	$adminpass = mandff($_POST['adminpass'], 'Invalid Admin Password');
	$adminemail = mandff($_POST['adminemail'], 'Invalid Email');
	
	$adminpass = md5($adminpass);
	
	if(!empty($error))
	{
		echo "Errors: <br />";
		foreach($error as $err)
			echo $err . "<br />";
		die();
		//die($error);
	}
	
	// take $dbhost, $dbuser, $dbpass, $dbconn from config file now, 
	// as config file has been written in previous step
	include('config.php');
	
	$conn = mysql_connect($host, $dbuser, $dbpass) or mysql_dead('No Db connection');
	$select_db = mysql_select_db($dbname) or mysql_dead('No DB selected');
	
	if($select_db)
	{
		
		$q = array();
		
		// ERROR: Fire a CREATE TABLE `users` , before Firing INSERT INTO `users`
		
		//$q = "INSERT INTO `users` (`username`, `password`, `email`, `user_level`) VALUES ('$adminuser', '$adminpass', '$adminemail', '1')";
		$q0 = "INSERT INTO `users` (`username`, `password`, `email`, `user_level`) VALUES(1, 'admin', 'e38561d99e538eb7d936acb92bd847b0', 'a1@a.com', 'a1.com', 'abc', 5, '', NULL)";
		//$mq = $mysql->mq($q);
		$mq = mysql_query($q0);
		
		//$exec = shell_exec("nohup mysql -u ".$dbuser." -p ".$dbpass . " " . $dbname . " < /opt/lampp/htdocs/www/forums/myForum/2/myforum_2.sql");
		
		
		/*
		if($dbconn)
		{
			//$dbmake = mysql_query("CREATE DATABASE IF NOT EXISTS " . $dbname) or die('DB NOT CREATED');
			//$select_db = mysql_select_db($dbname, $dbconn) or die('Could not select DB');
		}
		*/
		
		
		// $exec = shell_exec("nohup mysql -u ".$dbuser." -p ".$dbpass . " " . $dbname . " < /opt/lampp/htdocs/www/forums/myForum/2/myforum_2.sql");
		
		include_once("sql/latest/queries.php");
		//include_once("myforum_3.php");
		
		global $q;
		
		//#// $m = $mysql->mq($q);
		// $m =1, just for timepass, for the moment 
		$m = 1;
		
		if ( is_array($q) ) 
		{
			foreach( $q as $k => $v)
			{
				mysql_query($v);
			}
		}
		else 
		{
			
		}
		
		// Write the config file here, on this step
		// take more info like to be written,
		// $sourcedir, $rootdir
		
		
		
		
		header("Location: install.php?step=3");
		exit(0);
	}

}


if(isset($_GET['step']) && $_GET['step'] == 2)
{
	
	include('config.php');
	global $globals;
	
	echo '
	<b>Please set the Admin information: </b>
	<br />
	<br />
	<form action="" method="post">
	Admin-Username: <br />
	<input type="text" name="adminuser" id="adminuser">
	<br />
	Admin-Pass: <br />
	<input type="password" name="adminpass" id="adminpass">
	<br />
	Admin-Email: <br />
	<input type="text" name="adminemail" id="adminemail"><br />
	<br />
	<input type="submit" value="Next >>" name="admin_set">
	</form>
	';
	
}


if(isset($_GET['step']) && $_GET['step'] == 3)
{
	echo 'Installation completed, you can now go to the 
	<a href="index.php">index</a> page to login.';
	
}


?>
