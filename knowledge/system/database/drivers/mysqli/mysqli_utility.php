<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2018, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2018, British Columbia Institute of Technology (http://bcit.ca/)
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 1.3.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * MySQLi Utility Class
 *
 * @package		CodeIgniter
 * @subpackage	Drivers
 * @category	Database
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/database/
 */
class CI_DB_mysqli_utility extends CI_DB_utility {

	/**
	 * List databases statement
	 *
	 * @var	string
	 */
	protected $_list_databases	= 'SHOW DATABASES';

	/**
	 * OPTIMIZE TABLE statement
	 *
	 * @var	string
	 */
	protected $_optimize_table	= 'OPTIMIZE TABLE %s';

	/**
	 * REPAIR TABLE statement
	 *
	 * @var	string
	 */
	protected $_repair_table	= 'REPAIR TABLE %s';

	
	/**
	 * Export
	 *
	 * @param	array	$params	Preferences
	 * @return	mixed
	 */
	protected function _backup($params = array())
	{
		if (count($params) === 0)
		{
			return FALSE;
		}

				extract($params);

				$output = '';

				if ($foreign_key_checks === FALSE)
		{
			$output .= 'SET foreign_key_checks = 0;'.$newline;
		}

		foreach ( (array) $tables as $table)
		{
						if (in_array($table, (array) $ignore, TRUE))
			{
				continue;
			}

						$query = $this->db->query('SHOW CREATE TABLE '.$this->db->escape_identifiers($this->db->database.'.'.$table));

						if ($query === FALSE)
			{
				continue;
			}

						$output .= '#'.$newline.'# TABLE STRUCTURE FOR: '.$table.$newline.'#'.$newline.$newline;

			if ($add_drop === TRUE)
			{
				$output .= 'DROP TABLE IF EXISTS '.$this->db->protect_identifiers($table).';'.$newline.$newline;
			}

			$i = 0;
			$result = $query->result_array();
			foreach ($result[0] as $val)
			{
				if ($i++ % 2)
				{
					$output .= $val.';'.$newline.$newline;
				}
			}

						if ($add_insert === FALSE)
			{
				continue;
			}

						$query = $this->db->query('SELECT * FROM '.$this->db->protect_identifiers($table));

			if ($query->num_rows() === 0)
			{
				continue;
			}

									
			$i = 0;
			$field_str = '';
			$is_int = array();
			while ($field = $query->result_id->fetch_field())
			{
								$is_int[$i] = in_array($field->type, array(MYSQLI_TYPE_TINY, MYSQLI_TYPE_SHORT, MYSQLI_TYPE_INT24, MYSQLI_TYPE_LONG), TRUE);

								$field_str .= $this->db->escape_identifiers($field->name).', ';
				$i++;
			}

						$field_str = preg_replace('/, $/' , '', $field_str);

						foreach ($query->result_array() as $row)
			{
				$val_str = '';

				$i = 0;
				foreach ($row as $v)
				{
										if ($v === NULL)
					{
						$val_str .= 'NULL';
					}
					else
					{
												$val_str .= ($is_int[$i] === FALSE) ? $this->db->escape($v) : $v;
					}

										$val_str .= ', ';
					$i++;
				}

								$val_str = preg_replace('/, $/' , '', $val_str);

								$output .= 'INSERT INTO '.$this->db->protect_identifiers($table).' ('.$field_str.') VALUES ('.$val_str.');'.$newline;
			}

			$output .= $newline.$newline;
		}

				if ($foreign_key_checks === FALSE)
		{
			$output .= 'SET foreign_key_checks = 1;'.$newline;
		}

		return $output;
	}

}
