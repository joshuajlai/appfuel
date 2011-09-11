<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Output;


use Appfuel\Framework\Exception,
	Appfuel\Framework\Output\HttpHeaderInterface;

/**
 * Controls adding http headers
 */
class HttpHeader implements HttpHeaderInterface
{
	/**
	 * @param	string	$text			header text
	 * @param	bool	$replace		flag to say this should replace not add
	 * @param	int		$responseCode	http reponse code
	 * @return	null
	 */
	public function add($text, $replace = true, $responseCode)
	{
		if (! is_string($text)) {
			throw new Exception("header text must be a string");
		}

		$replace =(bool) $replace;

		if (! is_int($responseCode)) {
			throw new Exception("header reponse code must be an integer");
		}

		header($text, $replace, $reponseCode);
		return $this;
	}

	/**
	 * @return	null
	 */
	public function addFileNotFound()
	{
		$this->header("Status: 404 Not Found");
	}

	/**
	 * @param	bool	$use	to use the cache
	 * @param	int		$max	number of seconds
	 * @return	null
	 */
	public function addCaching($use, $max)
	{
		$use =(bool) $use;
		if (true === $use) {
			$expire = strtotime('now') + $max;
			$cache  = "max-age=$max";
		}
		else {
			$expire = strtotime('-1 year');
			$cache  = 'no-cache';
		}

		$cache .= ', must-revalidate';
		$date = gmdate("D, d M Y H:i:s", $expire) . " GMT";
	
		$this->add("Cache-Control: $cache")
			 ->add("Expires: $data");
	}

	/**
	 * @param	string	$file			name of the file to stream
	 * @param	string	$disposition	inline or attachment
	 * @param	string	$mime			mime type
	 * @param	int		$size			size of the file
	 * @return	null
	 */
	public function addFileStreaming($file, $disposition, $mime, $size = null)
	{
		$file = mb_convert_encoding($file, 'ISO-8859-1', 'UTF-8');
		$this->add("Pragma: public")
			 ->add("Content-Transfer-Encoding: binary")
			 ->add("Content-Disposition: $disposition ; filename='$file'")
			 ->add("Content-Type: $mime");

		if (null !== $size) {
			$this->add("Content-Length: $size");
		}
		
	}
}
