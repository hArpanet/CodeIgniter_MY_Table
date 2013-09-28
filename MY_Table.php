<?php
/**
 * hArpanet Total Table Override (hATTO) - CodeIgniter MY_Table Library
 *
 * Now (mostly) PSR-2 Compliant
 * (https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation version 2.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * @package   CodeIgniter\Table\MY_Table
 * @author    hArpanet.com <mytable@harpanet.com>
 * @copyright 2012 hArpanet dot com
 * @license   http://www.gnu.org/licenses/gpl-2.0.html (GNU General Public License)
 * @version   1.4.10 @ 130528
 * @link      http://harpanet.com/programming/php/codeigniter/my_table
 * @see       Based on: http://codeigniter.com/wiki/HTML_Table_Library_helper_extension
 *
 */

/*
 * VERSION HISTORY
 *
 * v1.4.10      hArpanet.com 22-May-2013
 *     Fix      changed static vars back to public vars
 *     Update   more PSR-2 standards applied
 * v1.4.9       hArpanet.com 14-May-2013
 *     Fix      table headings were not processing attributes correctly in some formats of passing them in
 *     Update   converted code style to PSR-2
 * v1.4.8       hArpanet.com 08-May-2013
 *     Update   add_heading_row() now allows 'TR' (case insensitive) as a parameter; value is then an attribute
 *              to be added to the HTML <tr> tag.
 *              Example of usage:
 *                  add_heading_row(array('TR'=>'class="main"'), array('first'));
 *                  would give this HTML: <tr class="main"><th>first</th></tr>
 * v1.4.7       hArpanet.com 29-Apr-2013
 *     Update   show_columns() now allows an array of fieldnamed as well as parameter list
 * v1.4.6       hArpanet.com 08-Feb-2013
 *     Fix      minor fix to _table_rows() to allow attributes to be added during <th> convertions
 * v1.4.5       hArpanet.com 08-Nov-2012
 *     Fix      _table_rows() now closing cells correctly when converted to <th> - was closing with </td>
 * v1.4.4b      hArpanet.com 29-Oct-2012
 *     New      class of 'screen_only' to paging buttons in paging() method
 * v1.4.4a      hArpanet.com 19-Oct-2012
 *     New      'th' key to _table_rows() method
 *     Fix      'td' key in _table_rows() method - additional attributes not working previously
 * v1.4.4       hArpanet.com 28-Sept-2012
 *     New      paging(), get_page_start(), get_page_len() methods;
 *              Example of usage:
 *                // get table paging buttons HTML (will default to 250 recs per page)
 *                tplSet('paging',  $this->table->paging());
 *                // get current page values to send to search
 *                $page_start     = $this->table->get_page_start();
 *                $page_length     = $this->table->get_page_len();
 *                // run a controller method to handle search criteria
 *                $this->search($page_start, $page_length);
 *
 * v1.4.3a      hArpanet.com 07-Sept-2012
 *     Update   All public methods now 'return $this'; Also updated some comments
 * v1.4.3       hArpanet.com 17-Aug-2012
 *     New      clear_column_functions() method;
 *                  This method clears all column functions for a specified column. Rarely necessary I imagine.
 * v1.4.2       hArpanet.com 23-Jul-2012
 *     Update   set_my_heading() method
 *                  Now accepts an array containing a list of heading names as well as comma separated list of headings
 * v1.4.1       hArpanet.com 20-Jul-2012
 *     New      $process_empty property
 *                  If false (default) then $this->empty_cells value is returned when a cell value is empty
 *                  If TRUE            then the cell is processed as normal. This allows cell_functions to be
 *                                     performed which may place a value in the cell or leave it empty.
 *                                     Use $this->table->process_empty = TRUE; in your methods to enable.
 * v1.4         hArpanet.com 04-Jul-2012
 *     New      Where clause to _cell_function
 *                  As the Where clause is always eval()'d, it ALWAYS has htmlentities applied to it to
 *                  prevent any major PHP code - this reduces the Where clause to simple comparison statements.
 *                  The Where clause accepts {placeholders} similar to the cell_function
 *     New      Multiple _cell_functions
 *                  You can now add multiple set_column_function()'s to each table cell.
 *                  This helps reduces the complexity of the function statement and removes the need for if() statements
 *                  in the cell function. You can now use a combination of multiple set_column_function()'s and Where
 *                  clauses to perform conditional actions.
 *     New      set_column_order() method
 *                  Allows the display order of data columns to be changed. Call this function with a list of all columns
 *                  to display, in the order specified. NOTE: If you used set_column_order(), you MUST specify ALL columns
 *                  to be displayed, otherwise they will be omitted from the output as if they had been passed to hide_column().
 *     New      show_column() method
 *                  This function is a wrapper for set_column_order() and is added for semantic completeness.
 *                  You can always used set_column_order() instead of show_column() but semantic it may not indicate what
 *                  your code is doing.
 *
 * v1.3         hArpanet.com 11-Jun-2012
 *     New      eval() processing within _cell_function()
 *                  $eval flag to allow parent Controller to enable/disable eval() - it is OFF by default for safety
 *                  $strip_php flag to allow parent Controller to enable/disable the stripping of PHP code from user data - ON by default
 *                  '#' shortcode in first character to enable eval() for this individual action only. Note: $eval MUST BE FALSE to use this!
 *                  '\#' if you do not wish the action to be eval()'d and the first character is a literal '#', you must escape it using '\'.
 *                  '=' shortcode in first character of action string (or second character if '#' specified) flags whether to prepend 'return '
 *                      to the action string - just a coding nicety to save you having to type 'return ' into every action string.
 *                      Note: $eval flag MUST BE TRUE to use this!. If $eval is false, an '=' in the first character is returned as a literal '=' symbol
 *     New      memory() array
 *     New      memSet($value [,$name]), memGet([$name]) functions
 *                  This memory array allows values from one cell function to be stored away using memSet( $value, $name ) then
 *                  retrieved in a later cell function using memGet(). This is useful where a calculation is performed in one cell action
 *                  and that calculation result is required to determine the output in a later action calculation.
 *                  If the optional $name parameter is NOT specified, values are stored in a memSet named 'clipboard' (not system clipboard).
 *
 * v1.2         hArpanet.com 13-Dec-2011
 *     New      $column_actions property;
 *     New      populate() method;
 *     New      generate() method;
 *     New      clear() method;
 *     New      _column_not_hidden() helper;
 *     New      _find_key() helper;
 *     Update   remove_column() method;
 *
 */

/**
 * Override standard CodeIgniter Table Library
 *
 * @category   Libraries
 * @package    CodeIgniter\Table\MY_Table
 * @subpackage Libraries
 * @author     hArpanet.com <mytable@harpanet.com>
 * @link       http://harpanet.com/programming/php/codeigniter/my_table
 */
Class MY_Table extends CI_Table
{

    protected  $columns;
    protected  $column_actions = array();     // holds various settings to apply to column cells array ['column_name']['setting:hide|function|link'] = 'value'
    protected  $column_order   = array();
    protected  $heading_extra  = array();     // holds additional table headings either 'before' or 'after' standard headings
                                                    //      Elements in 'before' are output before normal table headings; 'after' output after normal table headings.
    protected  $memory         = array();     // a basic memory clipboard associative array for storing action result values. format is: 'varName'=>'value';

    var  $process_empty  = true;       // bool flag indicating whether we should return $this->empty_cells value ($process_empty = FALSE)
                                        // or run column function ($process_empty = TRUE) when cell data is empty or NULL
    var  $strip_php      = true;        // bool flag indicating whether we should use htmlentities() in
                                        // _cell_function() to disable any attempts to inject PHP code from user data - ON by default for safety
    var  $eval           = false;       // bool flag indicating whether we should use eval() in _cell_function() - OFF by default due to potential security issues
    var  $clear_memory   = false;       // bool flag indicating whether to clear memory when clear() method called
                                        // set to FALSE if you need to have values remembered between multiple tables
                                        // set to TRUE if you specifically need to clear all values


    /**
     * Construct Class
     */
    public function __construct()
    {
        log_message('debug', "MY_Table Class Initialized");
        parent::__construct();
    }

    // --- STRUCTURE ---------------------------------------------------

    /**
     * Populate the table array for processing
     *
     * @param mixed $table_data Either an array of data or a database result object
     *
     * @return object
     */
    public function populate($table_data=null)
    {
        if (! is_null($table_data)) {

            if (is_object($table_data)) {

                $this->_set_from_object($table_data);

            } elseif (is_array($table_data)) {

                $set_heading = (count($this->heading) == 0 && $this->auto_heading == true);
                $this->_set_from_array($table_data, $set_heading);
            }
        }

        return $this;
    }

    /**
     *  Generate the table - overriding CI_ table generate()
     *  The original CI_ script still showed columns that had been removed.
     *  There also appears to be a bug in the CI_ script -
     *
     * @param mixed $table_data Either an array of data or a database result object
     *
     * @return  string             HTML formatted table
     */
    public function generate($table_data=null)
    {
        // populate the local table data array (if data was supplied)
        $this->populate($table_data);

        // Is there anything to display?  No?  Smite them!
        if (count($this->heading) == 0 AND count($this->rows) == 0) {

            return; // 'Undefined table data';
        }

        // Compile and validate the template data
        $this->_compile_template();

        // BUILD THE TABLE
        $out  = $this->template['table_open'];
        $out .= $this->newline;

        $out .= $this->_table_caption();
        $out .= $this->_table_heading();
        $out .= $this->_table_rows();

        $out .= $this->template['table_close'];

        // Clear table class properties before returning the table
        $this->clear();

        return $out;
    }

    /**
     * Clears the table arrays.  Useful if multiple tables are being generated
     *
     * @access  public
     * @return  void
     */
    public function clear()
    {
        $this->strip_php        = true;
        $this->eval             = false;

        $this->auto_heading     = true;
        $this->heading          = array();
        $this->rows             = array();
        $this->column_actions   = array();
        $this->column_order     = array();

        if ($this->clear_memory) {
            $this->memory       = array();
        }

        return $this;
    }

    /**
     * Transpose table rows (note: any existing headings will be removed)
     *
     * @return object
     */
    public function transpose()
    {
        if (! empty($this->heading)) {

            $this->heading = array(); // Empty heading
        }

        $this->rows = $this->_flip($this->rows);

        return $this;
    }

    // --- HEADINGS ----------------------------------------------------

    /**
     *  set_my_heading() is an abstract function which offers a simpler format for calling set_heading()
     *
     *  set_heading() expects its data to be in an array element named 'data' which adds unnecessary
     *  complexity when creating table headings - see comparison examples below.
     *
     *  set_heading()
     *
     *    Parameter Format: array('data' => array('column_id' => 'column HTML'), 'attributeID' => 'attributeValue', ...), ...);
     *
     *    Eg.
     *    $this->table->set_heading(array('data' => array('id' => 'ID'), 'class' => 'myTH', 'style' => 'color:red'),
     *                              array('data' => array('sku' => 'SKU')),
     *                              array('data' => array('name' => 'Name')),
     *                              array('data' => array('price' => 'Price')),
     *                              array('data' => array('url_path' => 'URLPATH'))
     *                              );
     *
     *  set_my_heading()
     *
     *    Parameter Format: array( 'column_id' => 'column HTML', 'attributeID' => 'attributeValue', ...), ...);
     *
     *    Eg.
     *    $this->table->set_my_heading(array('id' => 'ID', 'class' => 'myTH', 'style' => 'color:red'),
     *                                 array('sku' => 'SKU'),
     *                                 array('name' => 'Name'),
     *                                 array('price' => 'Price'),
     *                                 array('url_path' => 'URLPATH')
     *                                 );
     *
     * @return object
     */
    public function set_my_heading()
    {
        $out  = array();
        $args = func_get_args();

        // if incoming argument was an array, get the data from it
        if (count($args) == 1 && is_array($args)) {
            $args = $args[0];
        }

        if (is_array($args) && isset($args[0])) {

            foreach($args as $heading) {

                // do we have an array of name and attribute pair(s) for this column?
                if (is_array($heading)) {

                    // yes, so first entry assumed to be column heading text
                    $first = true;

                    foreach($heading as $key => $data) {

                        if ($first) {

                            // first entry in array is the heading name
                            $out[]['data'] = $data;
                            // flag that we've grabbed the column heading (all others entries should be attributes)
                            $first = false;

                        } else {

                            // all entries after first are html attribute key=>value pairs

                            // internal array pointer is not automatically increased when items are added,
                            // so do it manually to ensure we're adding the attributes to the correct element
                            end($out);

                            // add attributes to output
                            $out[key($out)][$key] = $data;
                        }
                    }

                } else {

                    // just a heading string specified, no array
                    $out[] = $heading;
                }
            }
        }

        // now that we have the parameters structured correctly,
        // call CI set_heading()
        $this->set_heading($out);

        return $this;
    }

    /**
     * Add extra table headings in addition to the ones set with set_my_heading()
     * hArpanet.com, 07-Sept-2012
     *
     * @param   string      $type   Either 'before' or 'after' indicating where the extra headings will appear
     */
    public function add_heading_row()
    {
        $args       = func_get_args();
        $position   = 'before';

        // by default, extra headings appear before standard ones
        // so just strip ':before' from the array if specified
        $key = array_search(':before', $args);
        if ($key !== false) {

            unset( $args[$key] );
        }

        // check for 'after' headings
        $key = array_search(':after', $args);
        if ($key !== false) {

            $position = 'after';
            unset( $args[$key] );
        }

        // add data to extra headings array
        $this->heading_extra[$position][] = $args;

        return $this;
    }

    // --- COLUMNS -----------------------------------------------------


    /**
     * Hide table column
     * Hide a column to prevent it being displayed.
     * After hiding a column, you can still reference it in any cell functions.
     *
     * @param   string  Specify multiple columns as comma-separated strings
     *                  E.g. hide_column('name','postcode',..)
     * @return  void
     */
    public function hide_column()
    {
        $args = func_get_args();

        foreach ($args as $column) {
            $this->column_actions[$column]['hidden'] = true;
        }

        return $this;
    }

    /**
     * This is purely a wrapper function for semantic purposes
     * It passes all params onto set_column_order()
     * set_column_order() will by default show only columns given to it (in the order specified),
     * creating a pseudo show_column() service under a different name.
     */
    public function show_column()
    {
        // get all parameters
        $args = func_get_args();

        // if incoming argument was an array, get the data from it
        if (count($args) == 1 AND is_array($args)) {

            $args = $args[0];
        }

        // pass all parameters onto set_column_order() method
        call_user_func_array (array("MY_Table","set_column_order"), $args);

        return $this;
    }

    /**
     * Allow the display order of columns to be specified
     *
     * @param   strings     multiple params containing column name
     *                      E.g. set_column_order('name', 'address1', 'postcode');
     */
    public function set_column_order()
    {
        // get all parameters as column names
        if (func_num_args() > 0) {

            $cols = func_get_args();
            $this->column_order = $cols;
        }

        return $this;
    }

    /**
     * Add column to table
     *
     * @param    mixed    Specify multiple columns as comma-separated strings or an array
     *                     E.g. add_column('name','postcode',..)
     *                          add_column(array('name','postcode',..))
     * @return void
     */
    public function add_column()
    {
        /*
         * TODO: add_columns() is NOT WORKING when trying to add additional columns
         * onto a data array pulled from a db result set
         * The main problem seems to be that the appropriate field names are
         * not being set for the data, plus, it would be necessary to specify
         * new data for each new column for every row returned from the db
         * which is not practical!
         */

        // Is heading empty?
        if (empty($this->heading)) {

            // no heading yet
            $columns    = $this->_flip($this->rows);
            $args       = func_get_args();
            $columns[]  = (is_array($args[0])) ? $args[0] : $args;
            $this->rows = $this->_flip($columns);

        } else {

            // Assume that the first element is heading
            $args       = func_get_args();
            $col        = (is_array($args[0])) ? $args[0] : $args;
            $heading    = array_shift($col);

            // add columns...
            $columns    = $this->_flip($this->rows);
            $columns[]  = $col;
            $this->rows = $this->_flip($columns);

            // ...and heading
            $this->heading[] = $heading;
        }

        return $this;
    }

    /**
     * Remove column from table
     * This physically removes the column, any associated actions and any
     * associated data from memory. You cannot reference these columns after
     * they have been removed.
     *
     * @param    string    Specify multiple columns as comma-separated strings
     *                     E.g. remove_column('name','postcode',..)
     * @return    void
     */
    public function remove_column()
    {
        $args = func_get_args();

        foreach ($args as $column) {

            // remove the column heading
            $headingKey = $this->_find_key($this->heading, $column);
            if ($headingKey) {

                unset($this->heading[$headingKey]);
            }

            // remove any actions
            unset($this->column_actions[$column]);

            // remove the data
            $columns = $this->_flip($this->rows);
            unset($columns[$column]);
            $this->rows = $this->_flip($columns);
        }

        return $this;
    }

    // --- CELLS -------------------------------------------------------

    /**
     * Set a function to be applied to each cell (matching 'where' clause) in specified column
     *
     * Multiple functions can be applied to the same cell
     * Functions can have an associated 'where' clause if required
     * Functions and Where clause can use column placeholders, E.g. {postcode}
     * See Docs for explanation of # and = shortcodes in $func string
     *
     * E.g. No Where Clause (all postcodes will be displayed in uppercase):
     *         set_column_function('postcode', '#=strtoupper("{postcode}");')
     *
     * E.g. With Where Clause (strtoupper() will only be performed on postcodes in the bs1 area):
     *         set_column_function('postcode', '#=strtoupper("{postcode}");', 'strpos("{postcode}","bs1")')
     *
     * @param  string    $column      column identifier to apply function to
     * @param  string    $func        function string to apply to all values in this column
     * @param  string    $where       (optional) limit $func so that it only applies to cells matching $where criteria
     */
    public function set_column_function($column=false, $func=false, $where=false)
    {
        if ($column && $func) {

            // set column function
            $this->column_actions[$column]['function'][] = $func;
            $this->column_actions[$column]['where'][]      = $where;
        }

        return $this;
    }

    /**
     * Clear all functions for specified column
     *
     *  @param  string    $column        column identifier to clear functions for
     */
    public function clear_column_functions($column=false)
    {
        if ($column) {
            // clear column elements
            unset($this->column_actions[$column]['function']);
            unset($this->column_actions[$column]['where']);
        }

        return $this;
    }


    // --- PERSISTENCE -------------------------------------------------

    /*
     * For ease of use the following private methods are not prepended with underscores
     * as they are used within column function statements...
     */

    /**
     * Store a value into the memory array for use between table cells or rows
     *
     * @param   mixed   $val    Value to store
     * @param   mixed   $name   Name of variable or default to internal 'clipboard'
     * @return  mixed           Value from stored variable
     */
    private function memSet($val, $name='clipboard') {
        $this->memory[$name] = $val;
        return $val;
    }

    /**
     * Retrieve a value from the memory array
     *
     * @param   mixed   $name   Name of variable or default to internal 'clipboard'
     * @return  mixed           Value from stored variable
     */
    private function memGet($name='clipboard')
    {
        if (array_key_exists($name, $this->memory)) {
            return $this->memory[$name];
        }
    }


    // --- PAGING ------------------------------------------------------

    /**
     * Display complete HTML for <<prev and next>> buttons to allow paging buttons for table data
     * @author  hArpanet.com
     * @version 28-Sept-2012
     *
     * @uses    This is not a standalone method. It requires code within your controller method to manage
     *          the paging of data. This method just provides the buttons!
     * @see     crreg_reports.php which uses paging within its search page.
     *
     * @param int       $page_start     Record row number to begin listing records from
     * @param int       $page_rows      Number of records being shown on each page
     *
     * Additional parameters can be received if $page_start is passed as an array...
     * @param int       start           Record row number to begin listing records from
     * @param int       rows            Number of records being shown on each page
     * @param string    url             URL to post data to when paging button clicked (defaults to current url)
     * @param bool      use_post        Flag indicating whether to place all $_POST vars into form as hidden values
     *                                  Default=TRUE which allows replication of any 'search' values on current page
     * @param array     data            As well as of pulling data from $_POST (depending on $use_post flag), an
     *                                  associative array can be passed and these values will be added to form as hidden values
     * @param bool      prev            Flag indicating whether to show <<prev button
     * @param bool      next            Flag indicating whether to show next>> button
     * @return                          HTML table containing two forms for <<prev or next>> buttons
     */
    public function paging($page_start='', $page_rows='')
    {
        // define allowed parameters and their default values
        $allowed = array('start'=>0, 'rows'=>0, 'url'=>'', 'use_post'=>true, 'data'=>array(), 'prev'=>true, 'next'=>true);

        $retval = '';
        $hidden = '';

        // initialise vars based on passed params
        foreach ($allowed as $param => $default) {

            // if page_start is an array, assign all passed parameters to variables
            if (is_array($page_start) && array_key_exists($param, $page_start)) {

                // create and set parameter variable
                $$param = $page_start[$param];

            } else {

                // no value passed so create variable and get default value
                $$param = $allowed[$param];
            }
        }

        //
        // overwrite default start/length values with passed param values
        //
        if ($start == 0                    // default value assigned, therefore not passed as a parameter value in an array
            && (!is_array($page_start))    // first parameter is not an array of values
            && $page_start != ''           // first parameter does contains a value
            && $page_start > 0)
            {
                $start = $page_start;      // assign the value of the passed parameter
            }

        if ($page_rows != '') {

            $rows = $page_rows;            // page_rows is not an issue as it will only ever be expected as a pure value
        }

        // add $_POST data if required
        if ($use_post) {

            foreach ($_POST as $key => $val) {

                // leave out the paging vars while collecting submitted $_POST values
                if (! in_array($key, array('paging_start', 'paging_rows', 'paging_prev', 'paging_next'))) {

                    $hidden .= "<input type='hidden' name='$key' value='$val'/>";
                }
            }
        }

        // add $data if required
        if (count($data)>0) {

            foreach ($data as $key => $val) {

                $hidden .= "<input type='hidden' name='$key' value='$val'/>";
            }
        }

        // get current page start and length from $_POST if available
        $start = $this->get_page_start($start);
        $rows  = $this->get_page_rows($rows);

        // build HTML output
        $retval .= "<table name='paging' class='centre'><tr>";

        // add <<prev link if relevant and not disabled
        if ($start > 1 && $prev === true) {

            $retval .= "<td class='centre'>";
            $retval .= form_open($url, array('class'=>'screen_only'));

                $retval .= $hidden;
                $retval .= "<input type='submit' name='paging_prev' value='&lt;&lt; prev' /> ";
                $retval .= "<input type='hidden' name='paging_start' value='".( ($start-$rows < 1) ? 1 : ($start-$rows) )."' />";
                $retval .= "<input type='hidden' name='paging_rows' value='".($rows)."' />";

            $retval .= "</form>";
            $retval .= "</td>";
        }

        if ($next === true) {

            // add next>> link if not diabled
            $retval .= "<td class='centre'>";
            $retval .= form_open($url, array('class'=>'screen_only'));

                $retval .= $hidden;
                $retval .= " <input type='submit' name='paging_next' value='next &gt;&gt;' />";
                $retval .= "<input type='hidden' name='paging_start' value='".($start+$rows)."' />";
                $retval .= "<input type='hidden' name='paging_rows' value='".($rows)."' />";

            $retval .= "</form>";
            $retval .= "</td>";
        }

        $retval .= "</tr></table>";

        return $retval;
    }

    /**
     * Returns a record number to show paged records from
     * The value is either passed in as a parameter, or taken from $_POST data
     * @author  hArpanet.com
     * @version 21-Sept-2012
     *
     * @param   int     $page_start     Integer containing starting record number
     * @param   mixed   $_POST          Various data values - hopefully also containing 'paging_start'
     * @return  int                     Valid record starting number (defaults to 1 if no value found)
     */
    public function get_page_start($page_start='')
    {
        if ($page_start == 1) $page_start == 0;

        // get page start passed as parameter with basic validation encoding
        if ($page_start != '') {

            $page_start = intval(htmlspecialchars($page_start));

        } else {

            // get page start passed as form values with basic validation encoding
            if (array_key_exists('paging_start', $_POST)) {

                $page_start = intval(htmlspecialchars($_POST['paging_start']));
            }
        }

        // NOTE: Default value MUST be greater than 0 otherwise paging is disabled
        $page_start = ($page_start < 1) ? 1 : $page_start;

        return $page_start;
    }

    /**
     * Returns the number of records to show per page
     * The value is either passed in as a parameter, or taken from $_POST data
     * @author  hArpanet.com
     * @version 21-Sept-2012
     *
     * @param   int     $page_length    Integer containing number of records per page
     * @param   mixed   $_POST          Various data values - hopefully also containing 'paging_length'
     * @return  int                     Valid page length number (defaults to 1 if no value found)
     */
    public function get_page_rows($page_rows='')
    {
        // get page start passed as parameter with basic validation encoding
        if ($page_rows != '') {

            $page_rows = intval(htmlspecialchars($page_rows));

        } else {

            // get page start passed as form values with basic validation encoding
            if (array_key_exists('paging_rows', $_POST)) {

                $page_rows = intval(htmlspecialchars($_POST['paging_rows']));
            }
        }

        // default page length to 250 if not specified
        $page_rows = ($page_rows < 1) ? 250 : $page_rows;

        return $page_rows;
    }


    // --- HELPERS -----------------------------------------------------

    /**
     * Flip an array to allow a later search through columns of data
     * using e.g. array_key_exists(), etc.
     *
     * @param   array   $array  Array to be flipped
     * @return  array           Flipped array
     */
    private function _flip($array)
    {
        $flipped = array();
        foreach ($array as $key => $subarray) {

            foreach ($subarray as $subkey => $subvar) {

                $flipped[$subkey][$key] = $subvar;
            }
        }

        return $flipped;
    }


    /**
     * Set a HTML <caption> for the generated table
     */
    private function _table_caption()
    {
        $out = "";

        if ($this->caption) {

            $out .= $this->newline;
            $out .= '<caption>'.$this->caption.'</caption>';
            $out .= $this->newline;
        }

        return $out;
    }


    /**
     * Set HTML heading <th> for each table column
     * This is a helper function used by the generate() method
     */
    private function _table_heading()
    {
        // initialise output string
        // $out will contain entire <thead> section
        $out = '';

        if (count($this->heading) > 0) {

            $out .= $this->template['thead_open'];
            $out .= $this->newline;

            /*
             *  PROCESS EXTRA 'BEFORE' HEADINGS
             */
            $out .= $this->_extra_headings('before');

            /*
             * PROCESS STANDARD TABLE HEADINGS
             */
            $out .= $this->template['heading_row_start'];
            $out .= $this->newline;

            foreach ($this->heading as $headID => $heading) {

                // pull header text (data element) from heading array
                $hdata = isset($heading['data']) ? $heading['data'] : $heading;
                $hkey  = is_array($hdata) ? key($hdata) : $hdata;   // won't be an array if passed as val1,val2,...

                // only process this cell if it is not hidden
                if ($this->_column_not_hidden($hkey)) {

                    // begin building row as temp in case modifers specified
                    $temp = $this->template['heading_cell_start'];

                    // output <th> tag with any specified attributes
                    foreach ($heading as $key => $val) {

                        if ('data' == $key) {

                            // just data, so add the cell value
                            $temp .= $val;

                        } else {

                            // check if we are altering the table ROW attributes (key='TR')
                            if ('TR' == $key OR 'tr' == $key) {

                                $out = str_replace('<tr', '<tr '.$val, $out);
                                $temp = '';

                            } else {

                                // normal attributes have been specified, so add them to <th> tag
                                $temp = str_replace('<th', "<th $key='$val'", $temp);
                            }
                        }
                    }

                    // now that the row has been processed, add it to output
                    $out .= $temp;

                    // close table heading tag
                    $out .= $this->template['heading_cell_end'];
                }
            }

            $out .= $this->template['heading_row_end'];
            $out .= $this->newline;

            /*
             *  PROCESS EXTRA 'AFTER' HEADINGS
             */
            $out .= $this->_extra_headings('after');

            /*
             *  CLOSE TABLE HEADER
             */
            $out .= $this->template['thead_close'];
            $out .= $this->newline;
        }

        return $out;
    }

    /**
     * Process extra heading rows
     * @param  string $position 'before' (default) or 'after'
     * @return string           HTML table heading row if relevant
     */
    private function _extra_headings($position='before')
    {
        $out = '';

        if (array_key_exists($position, $this->heading_extra)) {

            // there may be more than one row of extra headings
            foreach ($this->heading_extra[$position] as $extra_row) {

                $out .= $this->template['heading_row_start'];
                $out .= $this->newline;

                // and there may (most likely) be multiple cells specified
                foreach ($extra_row as $heading_cells) {

                    $temp = $this->template['heading_cell_start'];

                    if (is_array($heading_cells)) {

                        // and there will (most likely) be multiple settings per cell
                        foreach ($heading_cells as $key => $val) {

                            // update <th> tag with any specified attributes
                            if ('data' == $key) {

                                // add the cell value
                                $temp .= $val;

                            } else {

                                // check if we are altering the table ROW attributes (key='TR')
                                if ('TR' == $key OR 'tr' == $key) {
                                    $out = str_replace('<tr', '<tr '.$val, $out);
                                    $temp = '';

                                } else {

                                    // normal attributes have been specified, so add them to <th> tag
                                    $temp = str_replace('<th', "<th $key='$val'", $temp);
                                }
                            }
                        }

                    } else {

                        $temp .= $heading_cells;
                    }
                    $temp .= $this->template['heading_cell_end'];

                    // add heading cell to output string
                    $out .= $temp;
                }

                $out .= $this->newline;
                $out .= $this->template['heading_row_end'];
                $out .= $this->newline;
            }
        }
        return $out;
    }


    /**
     *  Determine if a column has NOT been hidden
     *  @return     false if column hidden / TRUE if column visible
     */
    private function _column_not_hidden($id)
    {
        // check if column properties exist
        if (array_key_exists($id, $this->column_actions)) {

            // column has properties set, so check for hidden property
            if (array_key_exists('hidden', $this->column_actions[$id])) {

                // column is hidden
                return false;
            }
        }

        // column not hidden
        return true;
    }


    /**
     * Main row processing workhorse
     * This is a helper function used by the generate() method
     *
     * Perform any table or cell functions and re-order table columns
     *
     * @return string
     */
    private function _table_rows()
    {
        // set a custom cell manipulation function to a locally scoped variable so its callable
        $function = $this->function;

        $out = "";
        if (count($this->rows) > 0) {

            $out .= $this->template['tbody_open'];
            $out .= $this->newline;

            $i = 1;
            foreach ($this->rows as $row) {

                if (! is_array($row)) {

                    break;
                }

                // We use modulus to alternate the row colors
                $name = (fmod($i++, 2)) ? '' : 'alt_';

                $outRow  = $this->template['row_'.$name.'start'];
                $outRow .= $this->newline;

                $thisRow = "";  // temporary var to collect data for this row (if $thisRowOrder not specified)

                // get column order - wrap cellkey names in {} for easy replacement
                $thisRowOrder = false;
                if (count($this->column_order) > 0) {

                    $thisRowOrder = '{';
                    $thisRowOrder.= implode("}{", $this->column_order);
                    $thisRowOrder.= '}';
                }

                foreach ($row as $cellkey => $cell) {

                    // only process this cell if it is not hidden
                    if ($this->_column_not_hidden($cellkey)) {

                        /*
                         * OPENING <TD>
                         */
                        $temp       = $this->template['cell_'.$name.'start'];
                        $cnvt_to_th = '';

                        // add any key=>val's to cell TD
                        foreach ($cell as $key => $val) {

                            if ($key != 'data') {

                                switch (strtolower($key)) {

                                    case 'tr':
                                        $outRow = str_ireplace('<tr', "<tr $val", $outRow);
                                        break;

                                    case 'th':
                                        // flag cell convertion from data to heading
                                        // (cannot do conv here as it will prevent other attributes being added)
                                        $cnvt_to_th = array('val'=>$val);
                                        break;

                                    default:
                                        $temp = str_ireplace('<td', "<td $key='$val'", $temp);
                                        break;
                                }
                            }
                        }
                        // perform cell convertion from data to heading
                        $temp = (is_array($cnvt_to_th)) ? str_ireplace('<td', "<th ".$cnvt_to_th['val'], $temp) : $temp;

                        // ADD TD to current cell
                        $thisCell = $temp;

                        /*
                         * ADD CELL DATA CONTENT
                         */

                        // get current cell data value
                        $temp = isset($cell['data']) ? $cell['data'] : '';

                        if (($temp === '' OR $temp === null) && $this->process_empty === false) {

                            $thisCell .= $this->empty_cells;

                        } else {

                            if ($function !== false && is_callable($function)) {

                                $thisCell .= $this->_cell_function($cellkey, call_user_func($function, $temp), $row);

                            } else {

                                $thisCell .= $this->_cell_function($cellkey, $temp, $row);
                            }
                        }

                        /*
                         * closing </TD>
                         */
                        $temp = $this->template['cell_'.$name.'end'];

                        // convert closing </td> element if opening element was modified
                        foreach ($cell as $key => $val) {

                            if ( $key != 'data') {

                                switch (strtolower($key)) {

                                    case 'th':
                                        // convert row from data to heading
                                        $temp = str_ireplace('</td', '</th', $temp);
                                        break;
                                }
                            }
                        }

                        // ADD TD to current cell
                        $thisCell .= $temp;

                        // put this cell data into ordered cells position
                        if ($thisRowOrder) {

                            $thisRowOrder = str_replace('{'.$cellkey.'}', $thisCell, $thisRowOrder);

                        } else {

                            $thisRow .= $thisCell;
                        }
                    }
                }

                // add current row data to outrow
                if ($thisRowOrder) {

                    // remove any straggling {placeholders} from $thisRowOrder
                    $thisRowOrder = preg_replace('(\\{.*\\})', '', $thisRowOrder);

                    // add ordered cells to outrow
                    $outRow .= $thisRowOrder;

                } else {

                    // add unordered cells to outrow
                    $outRow .= $thisRow;
                }

                $outRow .= $this->template['row_'.$name.'end'];
                $outRow .= $this->newline;

                $out .= $outRow;
            }

            $out .= $this->template['tbody_close'];
            $out .= $this->newline;
        }

        return $out;
    }


    /**
     * parse the {placeholders} in specified $cell and eval() any associated cell functions
     *
     * @param   string      cellkey     table column name being processed
     * @param   string      cell        table cell data
     * @param   array       row         entire row of table data - this is necessary to allow {placeholders}
     *                                  in this cell to pull data from other cells in the row
     * @return  string                  Processed $cell data
     */
    private function _cell_function($cellkey, $cell, $row)
    {
        // check if this column has any actions associated with it
        if (array_key_exists($cellkey, $this->column_actions)) {
            /*
             * PROCESS COLUMN FUNCTION action
             */
            if (array_key_exists('function', $this->column_actions[$cellkey])) {

                // process all functions set for this cell
                foreach ($this->column_actions[$cellkey]['function'] as $key => $action) {

                    // get Where clause for this cell
                    $where = $this->column_actions[$cellkey]['where'][$key];

                    // pull {placeholder} data from other columns if they are referenced in the current cell
                    foreach ($row as $key => $value) {

                        // handle null data values
                        $celldata = is_null($value['data']) ? '' : $value['data'];
                        // $celldata = $value['data'];

                        // replace {placeholders} with relevant values from current row
                        if ($this->strip_php) {

                            // attempt to disable any injection attempts using htmlentities()
                            $action = str_replace("{".$key."}", htmlentities($celldata), $action);
                            $where  = str_replace("{".$key."}", htmlentities($celldata), $where);

                        } else {

                            // take user data as presented - WARNING!!! POTENTIAL SECURITY THREAT!!!
                            $action = str_replace("{".$key."}", $celldata, $action);
                            $where  = str_replace("{".$key."}", $celldata, $where);
                        }
                    }

                    /*
                     * WARNING===WARNING=== EVAL IN OPERATION ===WARNING===WARNING
                     */

                    // eval the Where clause if one exists - ALWAYS strip PHP from where clause to reduce security issues
                    // this reduces the Where clause to simple statements. E.g. foo==bar; foo!=bar;

                    // TODO: htmlentities is making eval() fail - disabled for now
                    //$isWhere = ($where) ? eval('return '.htmlentities($where).';') : true;
                    $isWhere = ($where) ? eval('return '.$where.';') : true;

                    // only perform cell function if Where clause active
                    if ($isWhere) {

                        // if activated, perform eval() on the action
                        // eval is active if either $eval flag is true, or the first character of action string is '#'
                        if ($this->eval OR substr($action, 0, 1)=='#') {

                            // strip eval shortcode if given
                            $action = trim($action, '#');

                            // do we need to add 'return' to the value (action begins with '=')?
                            // If not, the action must have its own return statement within it
                            if (substr($action, 0, 1) == "=") {

                                $action = "return ".substr($action, 1);
                            }

                            // perform the eval()
                            $result = eval($action);

                        } else {

                            // no eval required or allowed
                            $result = false;
                        }

                        // strip escaped '#' if present ( i.e. '\#' specified )
                        $action = (substr($action, 0, 2)=='\#') ? substr($action, 1) : $action;

                        // if eval failed or not allowed, return original cell value but with placeholders replaced
                        // otherwise return result of the eval()
                        $cell = ($result) ? $result : $action;
                    }
                }
            }
        }

        // return modified cell contents
        return $cell;
    }


    /**
     * Find the index of a named key within 'data' element of passed array
     * This is a helper function used by remove_column() method
     *
     * @param mixed $arry
     * @param mixed $key
     */
    private function _find_key($arry, $key)
    {
        foreach ($arry as $ind=>$val) {

            if ($val['data'] == $key) {

                return $ind;
            }
        }

        return false;
    }

    // -----------------------------------------------------------------

}
