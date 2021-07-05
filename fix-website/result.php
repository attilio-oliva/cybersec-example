<head>
<link rel="stylesheet" href="style.css">
</head>
<header>
	<h1>FFMPEG time cutter tool online</h1>
</header>
<body>
<div class="centered">
<?php
error_reporting(E_ERROR | E_PARSE);

$upload_dir = "uploaded/";
$target_dir = "processed/";
$start_time = $_POST["start"];
$end_time = $_POST["end"];

$file_name = $_FILES["fileToUpload"]["name"];
$uploaded_file = $upload_dir . $file_name;
$file_type = pathinfo($uploaded_file);
$base_filename = $file_type["filename"];
$file_ext = $file_type["extension"];
$target_file = $target_dir . "dummy.mp4";

$time_pattern = "/^[0-9]{1,9}$/";
$timestamp_pattern = "/^[0-9]{2}\:[0-9]{2}\:[0-9]{2}$/";
$timestamp_ms_pattern = "/^[0-9]{2}\:[0-9]{2}\:[0-9]{2}\.[0-9]{1,3}$/";

$is_input_set = (isset($start_time) && isset($end_time));
$is_file_valid = (isset($_FILES["fileToUpload"]) && !empty($_FILES["fileToUpload"]["tmp_name"]));

$is_start_valid = preg_match($time_pattern, $start_time) || preg_match($timestamp_pattern, $start_time) || preg_match($timestamp_ms_pattern, $start_time);
$is_end_valid = preg_match($time_pattern, $end_time) || preg_match($timestamp_pattern, $end_time) || preg_match($timestamp_ms_pattern, $end_time);

if(!$is_input_set || !$is_file_valid || !$is_start_valid || !$is_end_valid)
{
	echo "Error, data provided was not valid";
	die();
}
//find an unused name for uploaded file
do
{
	$file_name = uniqid()  . '.' . $file_ext;
	$uploaded_file = $upload_dir . $file_name;
	$target_file = $target_dir . $file_name;
}
while(file_exists($uploaded_file) || file_exists($target_file));

//move file to uploaded directory
if (!move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $uploaded_file))
{
	echo "Sorry, there was an error uploading your file.";
	die();
}

$cmd = "ffmpeg -ss $start_time -i \"$uploaded_file\" -to $end_time -c:a copy \"$target_file\" 2>&1"; 
exec($cmd, $outval, $retval);
//$outval = shell_exec($cmd,$retval);
//echo $outval;
if($retval == 0)
{
	//No error show the video
	echo "<p>Video processed succefully</p>";
	echo "<video width='720' height='480' controls>";
	echo "<source src=\"$target_file\" type='video/mp4'>";
	echo "Your browser does not support the video tag.";
	echo"</video>";
}
else
{
	echo "Error occurred during video processing";
}
//remove uploaded file
unlink(urldecode($uploaded_file));
?>
</div>
</body>
