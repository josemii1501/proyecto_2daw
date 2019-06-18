<!DOCTYPE html>
<html>
<head>
	<title>Video Convert By PHP</title>
</head>
<body>
	<form action="" method="post" enctype="multipart/form-data">
		<input type="file" name="video">
		<input type="submit" name="submit">
	</form>
</body>
</html>
<?php 
if (isset($_POST['submit'])) {
	$currentPath=$_FILES['video']['tmp_name'];
	exec("ffmpeg.exe"); 
	exec("ffmpeg -i ".$currentPath." -an ./output/video.mp4");

	echo "Ok";
	
}



 ?>