<?php
/*
JLFunctions MD Class
Copyright (c)2009-2011 John Lamansky
*/

class sumd {
	
	/**
	 * Retrieves the content of a specific section of an MD document.
	 * 
	 * @param string $md The MD document, with Windows-style newlines.
	 * @param string $path The header of the section to retrieve. Nested headers are separated by slashes. If headers contain actual slashes, escape those slashes with backslashes. For example: "Header/Subheader/Sub-sub-header/Regarding A and\\/or B". If the number of headers is greater than $nms, the extra headers will be ignored.
	 * @param int $nms The number of minus signs that top-level headers will have. If $nms=2, then top header will look like <code>== Header ==</code>.
	 * @return string The section specified by the $path.
	 */
	function get_section($md, $path, $nms=2) {
		
		//Permit escaped slashes.
		$path = str_replace("\\/", "<SLASH>", $path);
		
		//Break up the path into header levels
		$levels = explode("/", $path);
		
		//Cycle through the header levels
		foreach ($levels as $level) {
			
			//Add in escaped slashes again
			$level = str_replace("<SLASH>", "/", $level);
			
			//Create the string that will prefix and suffix the header text
			$m = str_repeat("=", $nms);
			
			//If the document contains the header specified...
			if ($levelstart = strpos($md, $levelheader = "\r\n\r\n$m $level $m\r\n\r\n")) {
			
				//Lop off everything in the document that comes before the header
				$md = substr($md, $levelstart + strlen($levelheader));
				
				//If another sibling (i.e. non-child) header comes afterwards, remove it and everything proceding so that we just have the section we want.
				//If no other sibling headers follow, then the section we want must continue to the end of the document.
				if ($levelend = strpos($md, "\r\n\r\n$m "))
					$md = substr($md, 0, $levelend);
			} else
				//One of the headers wasn't found, so this specific path must not exist. Return empty string.
				return '';
			
			//Now we'll go one header level down.
			$nms--;
			
			//If we've reached the end, break the loop.
			if ($nms == 0) break;
		}
		
		return $md;
	}
	
	/**
	 * Gets all sections of an MD document and returns them in an array.
	 * 
	 * @param string $md The MD document.
	 * @return array An array of header text => section content.
	 */
	function get_sections($md) {
		
		$md = "\r\n$md";
		
		$sections = array();
		
		//Get MD sections
		$preg_sections = preg_split("|\r\n=+ ([^=]+) =+\r\n|", $md, null, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
		$preg_sections = array_chunk($preg_sections, 2);
		
		foreach ($preg_sections as $preg_section) {
			$header  = isset($preg_section[0]) ? trim($preg_section[0]) : '';
			$content = isset($preg_section[1]) ? trim($preg_section[1]) : '';
			if (strlen($header))
				$sections[$header] = $content;
		}
		
		return $sections;
	}
	
	function convert_headers($md, $h) {
		return trim(preg_replace('|\r\n=+ ([^=]+) =+\r\n|', "<$h>\\1</$h>", "\r\n$md\r\n"));
	}
}

?>