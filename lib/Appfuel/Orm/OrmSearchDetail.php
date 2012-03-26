<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Orm;

use InvalidArgumentException;

/**
 * Value object used to hold details about a search. The main details
 * needed are generally for pagination
 */
class OrmSearchDetail
{
	/**
	 * Max items returned
	 * @var int
	 */
	protected $perPage = null;

	/**
	 * offset of recordset to return from
	 * @var int
	 */
	protected $pageNbr = null;

	/**
	 * Direction of the sort
	 * @var string
	 */
	protected $dir = 'asc';

	/**
	 * field to sort against
	 * @var string
	 */
	protected $sortField = null;

	/**
	 * search term used 
	 * @var string
	 */
	protected $term = null;

	/**
     * @return  array	$data
     */
    public function __construct(array $data)
    {
		$perPageDefault = 50;
		$perPageMin     = 0;
		$perPageMax     = 500;
		$perPage		= $perPageDefault;
		if (isset($data['per-page-default']) && 
			is_int($data['per-page-default'])) {
			$perPageDefault = $data['per-page-default'];
		}

		if (isset($data['per-page-min']) && is_int($data['per-page-min'])) {
			$perPageMin = $data['per-page-min'];
		}

		if (isset($data['per-page-max']) && is_int($data['per-page-max'])) {
			$perPageMax = $data['per-page-max'];
		}

		if (isset($data['count']) && is_int($data['count'])) {
			$perPage = $data['count'];
		}
		$this->setPerPage($perPage, $perPageMin, $perPageMax, $perPageDefault);
 
		$pageNbrDefault = 1;
		$pageNbrMin = 0;
		$pageNbrMax = 500;
		$pageNbr = $pageNbrDefault;
		if (isset($data['page-nbr-default']) && 
			is_int($data['page-nbr-default'])) {
			$pageNumDefault = $data['page-nbr-default'];
		}

		if (isset($data['page-nbr-min']) && is_int($data['page-nbr-min'])) {
			$perPageMin = $data['page-nbr-min'];
		}

		if (isset($data['page']) && is_int($data['page'])) {
			$pageNbr = $data['page'];
		}
		$this->setPageNbr($pageNbr, $pageNbrMin, $pageNbrMax, $pageNbrDefault);

		if (isset($data['dir']) && 
			is_string($data['dir']) &&
			'desc' === strtolower($data['dir'])) {

			$this->dir = 'desc';
		}

		if (isset($data['col'])) {
			$this->setSortField($data['col']);
		}

		if (isset($data['search'])) {
			$this->setSearchTerm($data['search']);
		}
	}

	/**
	 * @return	int
	 */
	public function getPerPage()
	{
		return $this->max;
	}

	/**
	 * @return	string
	 */
	public function getPageNumber()
	{
		return $this->pageNbr;
	}

	/**
	 * @return	string
	 */
	public function getSortDirection()
	{
		return $this->sortDir;
	}

	/**
	 * @return	mixed
	 */
	public function getSortField()
	{
		return $this->sortField;
	}
	
	/**
	 * @return	string
	 */
	public function getSearchTerm()
	{
		return $this->searchTerm;
	}

	/**
	 * @param	int		$nbr
	 * @param	int		$min
	 * @param	int		$max
	 * @param	int		$default
	 * @return	null
	 */
	protected function setPerPage($nbr, $min, $max, $default)
	{
		$nbr =(int) $nbr;
        if ($nbr <= $min || $nbr >= $max) {
			$nbr = $default;
        }
		
		$this->perPage = $nbr;
	}

	/**
	 * @param	int		$nbr
	 * @param	int		$min
	 * @param	int		$max
	 * @param	int		$default
	 * @return	null
	 */
	protected function setPageNbr($nbr, $min, $max, $default)
	{
		$nbr =(int) $nbr;
        if ($nbr <= $min || $nbr >= $max) {
			$nbr = $default;
        }
		
		$this->pageNbr = $nbr;
	}

	/**
	 * @param	string	$field
	 * @return	null
	 */
	protected function setSortField($field)
	{
		if (! is_string($field)) {
			$err = "sort field must be a valid string";
			throw new InvalidArgumentException($err);
		}

		$this->sortField = $field;
	}

	/**
	 * @param	string	$term
	 * @return	null
	 */
	protected function setSearchTerm($term)
	{
		if (! is_string($term)) {
			$err = "search field must be a valid string";
			throw new InvalidArgumentException($err);
		}

		$this->term = $term;
	}
}
