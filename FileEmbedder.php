<?php

class FileEmbedder
{
	// Variables:
	private $filename;
	private $file_ext;
	private $filepath;
	
	// Constructor. Takes the path to the file (including the filename).
	public function __construct( $n_path )
	{
		$this->filepath = $n_path;
	}
	
	// Another constructor. Call using "FileExtension::FileExtension_extra(...)"
	// Takes the filename, filepath, and file extension
	public static function FileExtension_extra( $n_name, $n_path, $n_ext)
	{
		$instance = new self( $n_path );
		$instance->filename = $n_name;
		$instance->file_ext = $n_ext;
		return $instance;
	}

	// Private helper function. Gets the name of the file from the path.
	private function extract_name()
	{
		$this->filename = substr( $this->filepath, strrpos($this->filepath,"/")+1 );
	}

	// Private helper function. Gets the file extension from the file path.
	private function extract_ext()
	{
		$this->file_ext = substr( $this->filepath, strrpos($this->filepath,".")+1 );
	}

	public function getName()
	{
		return $this->filename;
	}
	
	public function getPath()
	{
		return $this->filepath;
	}
	
	public function display_file()
	{
		// Check for required variables. If path not found, display error.
		// If name or extension not found, extract them from file path.
		if( empty($this->filepath) ) { exit("ERROR: Path to file not found. Can't display file."); }
		if( empty($this->file_ext) ) { $this->extract_ext(); }
		if( empty($this->filename) ) { $this->extract_name(); }
		
		// Check the file extension and take proper action to display
		if( $this->file_ext === "pdf" )
		{
			echo "<object class=\"file\" data=\"" . $this->filepath . "\" type=\"application/pdf\">\n\t\t\t<p>Your browser is unable to display this pdf. Download it by clicking <a href=\"" . $this->filepath . "\">here</a>.</p>\n\t\t</object>\n";
		}
		elseif( $this->file_ext === "txt" )
		{
			$fh = fopen($filepath, 'r');
			$file_contents = fread($fh, filesize($this->filepath));
			$file_contents = str_replace("\n","<br/>",$file_contents);
			echo "<p id=\text_file\">" . $file_contents . "</p>";
		}
		else	// If file extension not listed, give option to download file.
		{
			echo "<p>The file type you are trying to view, <a href=\"" . $this->filepath . "\">" . $this->filename . "</a>, is not supported by this website.</p>";
		}
	}
}
?>
