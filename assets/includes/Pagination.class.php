<?php
/**
 *
 *
 */
class Pagination {

	/**
	 * Current page of the pagination
	 *
	 * @var integer
	 */
	var $current_page = 1;
	
	/**
	 * Quantity of items to show per page
	 *
	 * @var integer
	 */
	var $items_per_page = 20;
	
	/**
	 * Quantity of items will be showed
	 *
	 * @var integer
	 */
	var $total_items;
	
	/**
	 * Quantity of pages will be necessary to show all records
	 *
	 * @var integer
	 */
	var $total_pages;
	
	/**
	 *
	 *
	 */
	function __construct()
	{
	}
	
	/**
	 *
	 *
	 *
	 */
	function set_total_items($total_items)
	{
		$this->total_items = (int)$total_items;
	}
	
	/**
	 * Get a string to represent a SQL LIMIT, so, it can be used in a SQL Query
	 *
	 * @return string
	 */
	function get_sql_limit()
	{
		return sprintf(' LIMIT %d, %d', ( ($this->current_page - 1) * $this->items_per_page), $this->items_per_page);
	}
	
	/**
	 *
	 *
	 *
	 */
	function get_total_items()
	{
		return $this->total_items;
	}
	
	/**
	 *
	 *
	 */
	function get_meta_information()
	{
		$from_item = ( $this->current_page == 1 ) ? 1 : ($this->current_page - 1) * $this->items_per_page + 1;
		$from_until = ( $this->total_items > ($this->current_page * $this->items_per_page) ) ? ($this->current_page * $this->items_per_page) : $this->total_items;
		return sprintf('<span class="displaying-num"><strong>Resultados</strong> %d - %d de %d <span>', $from_item, $from_until, $this->total_items);
	}
	
	/**
	 *
	 *
	 *
	 */
	function get_navigation()
	{
		$html  = "\n" . '<div class="tablenav-pages">';
		// Show the link to first page?
		if ( $this->current_page > 1 ) :
			$html .= "\n" . '<a class="page-numbers" href="?paged=1' . $this->_get_query_string() . '" title="Back to First Page"><span>&laquo; First</span></a>';
		endif;
		if ( $this->current_page > 1 ) :
			$html .= "\n" . sprintf('<a class="page-numbers" href="?paged=%d%s" title="Back to Previous Page"><span>&laquo; Previous</span></a>', $this->current_page - 1, $this->_get_query_string());
		endif;
		if ( $this->current_page != $this->_get_total_pages() ) :
			$html .= "\n" . sprintf('<a class="page-numbers" href="?paged=%d%s" title="Go to Next Page"><span>&raquo; Next</span></a>', $this->current_page + 1, $this->_get_query_string());
		endif;
		// Show the link to last page?
		if ( $this->_get_total_pages() != $this->current_page ) :
			$html .= "\n" . sprintf('<a class="page-numbers" href="?paged=%d%s" title="Go to Next Page"><span>&raquo; Last</span></a>', $this->_get_total_pages(), $this->_get_query_string() );
                endif;
		$html .= "\n" . '</div>';
		return $html;
	}
	
	/**
	 *
	 *
	 *
	 */
	function _get_query_string($query_to_avoid = 'paged')
	{
		// We already have a Query String?
		if ( !empty($_SERVER['QUERY_STRING']) ) :
			// Split the Query String
			$params = explode('&', $_SERVER['QUERY_STRING']);
			// The Array used to store the Query string desired
			$newParams = array();
			// Loop through
			foreach($params as $param) :
				// Avoid the query "page"
				if ( stristr($param, $query_to_avoid) === false )
					array_push($newParams, $param);
			endforeach;
			if ( count($newParams) > 0 )
				$queryString = '&amp;' . htmlentities(implode('&', $newParams));
			return $queryString;
		endif;
	}
	
	/**
	 * Calculate how many pages will be necessary to show all records
	 *
	 * @return integer
	 */
	function _get_total_pages()
	{
		return ceil($this->total_items / $this->items_per_page);
	}	
}
?>