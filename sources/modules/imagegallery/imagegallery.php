<?php

if(!defined('F'))
	die('You are doing the wrong thing son.');

function imagegallery()
{
	
	global $user;
	global $theme;
	global $user, $reqPrivs, $qe, $error, $errors;
	
	//~$theme['folder'] = __FUNCTION__;
	//~$theme['name'] = __FUNCTION__;
	
	$theme['call_theme_func'] = __FUNCTION__;
	$theme['page_title'] = 'Image Gallery';
	
	$uid = $user['uid'];
	$query = "SELECT * FROM `imagegallery_albums` WHERE `user_id`= $uid;";
	$qe = db_query($query);
	
}

// List All the Images inside the Album, by taking the Album Id
function viewalbum()
{
	global $themedir;
	global $globals, $mysql, $theme, $done, $errors, $notice;
	global $l;
	global $time;
	global $user, $reqPrivs, $qe;
	
	//~$theme['folder'] = 'imagegallery';
	//~$theme['name'] = 'imagegallery';
	
	$theme['call_theme_func'] = __FUNCTION__;
	$theme['page_title'] = 'Image Gallery: View Album';
	
	//~loadlang();
	//~fheader($title = 'View Profile');
	//~fheader("View Profile");
	
	// Base64encode for everything coming from URL
	// Checking input, checking everything coming from $_GET url, 
	// sanitizing it, and casting it into an (int) datatype
	$uid = ( isset($_GET["albid"] ) ? (int) check_input( $_GET["albid"] ) : null );
	
	// Add if $user['uid'] != $_GET['uid'] , then, see if he is Admin or Editor
	// Else, Not allowed to access this area, permission denied & return false
	// ---Permission stuff here---
	
	//~$query  = "SELECT * FROM `users` `u` RIGHT JOIN `profile` `p` ON `u`.`uid`=`p`.`users_uid` WHERE `u`.`uid`=$uid";
	$query  = "SELECT * FROM `imagegallery_photos` `p` RIGHT JOIN `users` `u` ON `p`.`album_id`=`u`.`uid` WHERE `u`.`uid`=$uid";
	
	// JOIN `banned` `b` on `u`.`uid`=`b`.`ban_uid`
	$qe = db_query($query);
	
}

// Create Album
function createalbum()
{
	
	global $user;
	global $theme;
	global $error, $errors, $row;
	
	
	//~$theme['folder'] = __FUNCTION__;
	//~$theme['lang'] = 'imagegallery';
	//~$theme['name'] = 'imagegallery';
	
	$theme['call_theme_func'] = __FUNCTION__;
	
	$theme['page_title'] = 'Image Gallery: Create Album';
	
	$done = false;
	
	if(isset( $_POST['submit'] ) && !empty($_POST['submit']))
	{
		$name = mandff(check_input($_POST['subject']), 'Name Empty' );
		$desc = mandff(check_input($_POST['desc']), 'Description Empty' );
		
		
		if($error || $errors)
			return false;
		
		
		$query  = "INSERT INTO `imagegallery_albums` 
			(
			`user_id`,
			`name`,
			`description`,
			`date`
			)
			VALUES ('$user[uid]', '$name', '$desc', NOW());
			";
			
		$q = db_query($query);
		
		$row = null;
		if($row = mysql_fetch_row($q) )
			$done = true;
		
		if( $row ) 
			$notice[] = "Album created, you can visit your album 
				<a href='$globals[ind]action=imagegallery&subaction=showalbum&albid=$row[id]'>here</a>
			 and start uploading pictures.";
		 
	}
	
}

// Almost DONE
// JUST CHECK EVERYTHING WORKING, 
// OR CHECK, ERROR CHECKING and Stuff 
function uploadimage()
{
	global $user;
	global $theme;
	global $error, $errors, $row;
	
	//~// Folder not getting used for the moment
	//~$theme['folder'] = __FUNCTION__;
	//~
	//~$theme['lang'] = 'imagegallery';
	//~$theme['name'] = 'imagegallery';
	$theme['call_theme_func'] = __FUNCTION__;
	$theme['page_title'] = 'Image Gallery: Upload Image';
	
	$albid = ( isset($_GET["albid"] ) ? (int) check_input( $_GET["albid"] ) : null );
	
	if(!$albid)
	{
		$error[] = 'No album id specified, Please go back and try again.';
		return false;
	}
	
	$allowedExts = array('gif', 'png', 'jpg', 'jpeg');
	
	if( isset($_POST['submit']) && !empty($_POST['submit']) )
	{
		
		$title = isset( $_POST['title'] ) ? mandff( $_POST['title'], 'Image Title Required') : null ;
		$description = isset( $_POST['description'] ) ? mandff( $_POST['description'], 'Image Description Required') : null ;
		$file = isset( $_POST['file'] ) ? mandff( $_POST['file'], 'Image File Required') : null ;
		
		$title = check_input($_POST['title']);
		$description = check_input($_POST['description']);
		//~$file = check_input($file);
		
		if( !empty( $_FILES ) )
		{
			
			$temp = explode(".", $_FILES["file"]["name"]);
			$extension = end($temp);
	
			//~$_FILES["file"]["name"];
			//~if (( ($_FILES["file"]["type"] == "image/gif") || ($_FILES["file"]["type"] == "image/jpeg") || ($_FILES["file"]["type"] == "image/jpg") || ($_FILES["file"]["type"] == "image/pjpeg") || ($_FILES["file"]["type"] == "image/x-png") || ($_FILES["file"]["type"] == "image/png")) && ($_FILES["file"]["size"] < 20000) && in_array($extension, $allowedExts))
			if (( ($_FILES["file"]["type"] == "image/gif") || ($_FILES["file"]["type"] == "image/jpeg") || ($_FILES["file"]["type"] == "image/jpg") || ($_FILES["file"]["type"] == "image/pjpeg") || ($_FILES["file"]["type"] == "image/x-png") || ($_FILES["file"]["type"] == "image/png"))  )
			{
				
				if( $_FILES["file"]["error"] > 0)
				{
					// show file upload error
					$error[] = 'There was a problem while uploading the file, please try again.';
					
					return false;
				}
				else
				{
					
					$y = date("Y");
					$m = date("m");
					$d = date("d");
					$pathname = "images/imagegallery/$y/$m/$d/";
					$file = $_FILES["file"]["name"];
					
					$fullPath = $pathname . $file;
					
					if( !file_exists( $fullPath ) )
					{
						$moved = null;
						
						// save as Folder 2013, month11, date 19
						if( !dirname($pathname) )
							mkdir ( $pathname, 0777, true );
						
						$moved = move_uploaded_file( $_FILES["file"]["tmp_name"], $fullPath );
						
						if( $moved )
						{
							
							$files_file_name = $_FILES['file']['name'];
							
							// INSERT INTO DB
							$query = "INSERT INTO `imagegallery_photos`
							(
								`user_id`, `album_id`, 
								`title`, `description`, 
								`actual_name` , `actual_file_name`, 
								`photo_save_path`, `date` 
							)
							VALUES
							(
								'$user[uid]', '$albid', 
								'$_POST[title]', '$_POST[desc]', 
								'$files_file_name', '$files_file_name',  
								'$fullPath', NOW()
							)";
							
							$row = db_query($query);
							
						}
						else
						{
							$error[] = "Could not move the file, file maybe in your temp folder. File: $file";
							return false;
						}
						
					}
					else
					{
						$error[] = "Files already exists";
						return false;
					}
					
				}
				
			}
			
		}
		
	}
	
}

function viewimage()
{
	global $user;
	global $theme;
	global $error, $errors, $qe, $imageid;
	
	// Folder not getting used for the moment
	$theme['folder'] = __FUNCTION__;
	
	$theme['lang'] = 'imagegallery';
	$theme['name'] = 'imagegallery';
	$theme['call_theme_func'] = __FUNCTION__;
	$theme['page_title'] = 'Image Gallery: View Image';
	
	$imageid = ( isset($_GET["imageid"] ) ? (int) check_input( $_GET["imageid"] ) : null );
	
	if(!$imageid)
	{
		$error[] = 'No album id specified, Please go back and try again.';
		return false;
	}
	
	$query = "SELECT * FROM `imagegallery_photos` WHERE `id`=$imageid";
	$qe = db_query($query);
	
}


// Main runs here
// and routes to other pages/functions
function _main()
{
	
	global $theme, $reqPrivs, $error;
	
	
	// Folder not getting used for the moment
	$theme['folder'] = __FUNCTION__;
	
	$theme['lang'] = 'imagegallery';
	$theme['name'] = 'imagegallery';
	$theme['call_theme_func'] = __FUNCTION__;
	$theme['page_title'] = 'Image Gallery: Main';
	
	$subaction = '';
	$subaction = isset( $_GET['subaction'] ) && !empty($_GET['subaction']) ? $_GET['subaction'] : '';
	
	if( $reqPrivs['board']['loginReq'] )
		if( !userUidSet() )
		{
			$error[] = "User not logged in. Please login and try again";
			//~redirect("$globals[boardurl]$globals[only_ind]action=login");
			return false;
		}
	
	switch($subaction)
	{
		case 'createalbum':
			// Call createalbum func
			createalbum();
			break;
			
		case 'viewalbum':
			// Call viewalbum with the specified album_id
			viewalbum();
			break;
		
		case 'viewimage':
			// Show image with specified image id
			viewimage();
			break;
			
		case 'uploadimage':
			uploadimage();
			break;
		
		default:
			// Else show main function
			imagegallery();
			// maybe break not needed, but still
			break;
		
	}
	
	
}




?>

