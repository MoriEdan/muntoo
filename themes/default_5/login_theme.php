<?php

// if not defined

//register_theme();

function login_theme()
{
	
	global $globals, $mysql, $done, $error, $errors;
	global $l;
	
	error_handler($error);
	error_handler($errors);
	
	if( $done )
	{
		//echo $l['thanks'] . '<a href="index.php?action=login">Login</a> here';
		echo $l['redirection'];
	}
	else
	{
		echo '<center>';
		echo $l['test_login_msg'];
		
		$action = $globals["only_ind_no_slash"].'action='.$_GET['action'];
		echo '
			<form action="'.$action.'" method="post">
				<table align="center">
					<tr>
						<td width="70%">'.$l['user_email'].'</td>
						<td><input type="text" name="email"> </td>
					</tr>
					<tr>
						<td>'.$l['pass'].'</td>
						<td><input type="password" name="password"></td>
					</tr>
				</table>
				<br />
				<center><input class="mun-button-default" type="submit" name="sub_register" value="Login"></center>
			</form>
			</center>
		';
	}
	
	
}



?>
