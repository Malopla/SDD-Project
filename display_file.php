<!--<?php
/*	function display_file( $filename )
	{
		$ext=substr( $filename, strrpos($filename,".")+1 );
		
		if( $ext === "pdf" )
		{
			echo "<object class=\"file\" data=\"" . $filename . "\" type=\"application/pdf\">\n\t\t\t<p>Your browser is unable to display this pdf. Download it by clicking <a href=\"" . $filename . "\">here</a>.</p>\n\t\t</object>\n";
		}
		elseif( $ext === "txt" )
		{
			$fh = fopen($filename, 'r');
			$file_contents = fread($fh, filesize($filename));
			$file_contents = str_replace("\n","<br/>",$file_contents);
			echo "<p id=\text_file\">" . $file_contents . "</p>";
		}
		else
		{
			
		}
	}*/
?>-->

<?php include './FileEmbedder.php'; ?>


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
//			$embedder = new FileEmbedder( "myfile.pdf", "./Resources/myfile.pdf", "pdf" );
			$embedder = new FileEmbedder( "./Resources/myfile.pdf" );
//			$embedder = FileEmbedder::FileExtension_extra("myfile.pdf", "./Resources/myfile.pdf", "pdf");
			$embedder->display_file();
/*			if( isset($file_display) )
			{
				echo $file_display;
			}
			else
			{
				echo "ERROR: file_display not set.";
			}*/
		?>
	</div>
</body>
</html>