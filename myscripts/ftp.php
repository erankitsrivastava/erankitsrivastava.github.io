<!-- 
	for better version of php ftp manager script please check this
	http://www.net2ftp.com 
-->
<!DOCTYPE html>
<html>
<body>

<?php

$ftp_server = isset($_POST['server'])?$_POST['server']:'server';
$ftp_user_name = isset($_POST['user'])?$_POST['user']:'user';
$ftp_user_pass = isset($_POST['password'])?$_POST['password']:'password';
$remote_dir = isset($_POST['remote_dir'])?$_POST['remote_dir']:'remote_dir ';

?>

<form method="post" enctype="multipart/form-data">
    Enter the server details:
    <input type="text" name="server" placeholder="server" value="<?php echo($ftp_server)?>">
    <input type="text" name="user" placeholder="user"  value="<?php echo($ftp_user_name)?>"> 
    <input type="text" name="password" placeholder="password"  value="<?php echo($ftp_user_pass)?>">
    <input type="text" name="remote_dir" value="/" placeholder="remote dir path"  value="<?php echo($remote_dir)?>">
    <input type="submit" value="Connect" name="submit">
</form>

<?php
// set up basic connection
$conn_id = @ftp_connect($ftp_server);

if ($conn_id) {
	
	// login with username and password
	$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);

	var_dump($login_result);
	//default values
	$file_url = "";
	echo "<pre>";
	if($login_result) {

		?>
			<form method="post" enctype="multipart/form-data">
			    Select file to upload:

			    <input type="hidden" name="server" placeholder="server" value="<?php echo($ftp_server)?>">
			    <input type="hidden" name="user" placeholder="user"  value="<?php echo($ftp_user_name)?>"> 
			    <input type="hidden" name="password" placeholder="password"  value="<?php echo($ftp_user_pass)?>">
			    <input type="hidden" name="remote_dir" value="/" placeholder="remote dir path"  value="<?php echo($remote_dir)?>">
			    <input type="file" name="file" id="file" webkitdirectory mozdirectory msdirectory odirectory directory multiple>
			    <input type="submit" value="Upload File" name="submit">
			</form>

		<?php
		//set passive mode enabled
		ftp_pasv($conn_id, true);
		// print_r(ftp_nlist($conn_id, '.'));
		//check if directory exists and if not then create it
		if(!@ftp_chdir($conn_id, $remote_dir)) {
			//create diectory
			ftp_mkdir($conn_id, $remote_dir);
			//change directory
			ftp_chdir($conn_id, $remote_dir);
		}

		if (!empty($_FILES)) {
			print_r($_FILES);	
			$file = $_FILES["file"]["tmp_name"];
			$remote_file = $_FILES["file"]["name"];

			$ret = ftp_nb_put($conn_id, $remote_file, $file, FTP_BINARY, FTP_AUTORESUME);
			while(FTP_MOREDATA == $ret) {
				$ret = ftp_nb_continue($conn_id);
			}

			if($ret == FTP_FINISHED) {
				echo "File '" . $remote_file . "' uploaded successfully.";
			} else {
				echo "Failed uploading file '" . $remote_file . "'.";
			}
		}
	} else {
		echo "Cannot connect to FTP server at " . $ftp_server;
	}

} else {
		echo "Cannot connect to FTP server at " . $ftp_server;
	}

?>


</body>
</html>