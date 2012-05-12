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
namespace Appfuel\Db\Mysql\Validate\Filter;

use Appfuel\Framework\Exception,
	Appfuel\Validate\Filter\ValidateFilter,
	Appfuel\Framework\DataStructure\DictionaryInterface;

/**
 * Allow only mysql charsets
 */
class CharsetFilter extends ValidateFilter
{
	/**
	 * List of mysql 5.5 charsets
	 * @var array
	 */
	protected $charsets = array(
		'big5', 'dec8', 'cp850', 'hp8', 'koi8r', 'latin1', 'latin2', 'swe7',
		'ascii', 'ujis', 'sjis', 'hebrew', 'tis620', 'euckr', 'koi8u', 
		'gb2312', 'greek', 'cp1250', 'gbk', 'latin5', 'armscii8', 'utf8',
		'ucs2', 'cp866', 'keybcs2', 'macce', 'macroman', 'cp852', 'latin7',
		'utf8mb4', 'cp1251', 'utf16', 'cp1256', 'cp1257', 'utf32', 'binary',
		'geostd8', 'cp932', 'eucjpms'
	);

	/**
	 * @return	string
	 */	
	public function filter($raw, DictionaryInterface $params)
	{
		if (empty($raw) || ! is_string($raw)) {
			return $this->failedFilterToken();
		}

		$raw = strtolower($raw);
		if (! in_array($raw, $this->charsets)) {
			return $this->failedFilterToken();
		}

		return $raw;
	}
}
