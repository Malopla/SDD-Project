<?php
	$filename="test.txt";
	$ext=substr( $filename, strrpos($filename,".")+1 );
	
	if( $ext === "pdf" )
	{
		$file_display = "<object class=\"file\" data=\"./Resources/" . $filename . "\" type=\"application/pdf\">
			<p>It appears you don't have a PDF plugin for this browser. No biggie... you can <a href=\"myfile.pdf\">click here to download the PDF file.</a></p>
		</object>";
	}
	elseif( $ext === "txt" )
	{
		$fh = fopen("./Resources/" . $filename, 'r');
		$file_contents = fread($fh, filesize("./Resources/" . $filename));
		$file_contents = str_replace("\n","<br/>",$file_contents);
		$file_display = "<div id=\text_file\">" . $file_contents . "</div>";
	}
?>


<!DOCTYPE html>
<html>
<head>
	<title>Display PDF</title>
	<style>
		html,body,#file_div
		{
			height: 100%;
		}
		object.file
		{
			height: 100%;
			width: 100%;
		}
	</style>
</head>
<body>
	<div id="file_div">
		<?php
			if( isset($file_display) )
			{
				echo $file_display;
			}
			else
			{
				echo "ERROR: file_display not set.";
			}
		?>
	</div>
</body>
</html>
