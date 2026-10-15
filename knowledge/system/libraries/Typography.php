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
 * @since	Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Typography Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Helpers
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/libraries/typography.html
 */
class CI_Typography {

	/**
	 * Block level elements that should not be wrapped inside <p> tags
	 *
	 * @var string
	 */
	public $block_elements = 'address|blockquote|div|dl|fieldset|form|h\d|hr|noscript|object|ol|p|pre|script|table|ul';

	/**
	 * Elements that should not have <p> and <br /> tags within them.
	 *
	 * @var string
	 */
	public $skip_elements	= 'p|pre|ol|ul|dl|object|table|h\d';

	/**
	 * Tags we want the parser to completely ignore when splitting the string.
	 *
	 * @var string
	 */
	public $inline_elements = 'a|abbr|acronym|b|bdo|big|br|button|cite|code|del|dfn|em|i|img|ins|input|label|map|kbd|q|samp|select|small|span|strong|sub|sup|textarea|tt|var';

	/**
	 * array of block level elements that require inner content to be within another block level element
	 *
	 * @var array
	 */
	public $inner_block_required = array('blockquote');

	/**
	 * the last block element parsed
	 *
	 * @var string
	 */
	public $last_block_element = '';

	/**
	 * whether or not to protect quotes within { curly braces }
	 *
	 * @var bool
	 */
	public $protect_braced_quotes = FALSE;

	/**
	 * Auto Typography
	 *
	 * This function converts text, making it typographically correct:
	 *	- Converts double spaces into paragraphs.
	 *	- Converts single line breaks into <br /> tags
	 *	- Converts single and double quotes into correctly facing curly quote entities.
	 *	- Converts three dots into ellipsis.
	 *	- Converts double dashes into em-dashes.
	 *  - Converts two spaces into entities
	 *
	 * @param	string
	 * @param	bool	whether to reduce more then two consecutive newlines to two
	 * @return	string
	 */
	public function auto_typography($str, $reduce_linebreaks = FALSE)
	{
		if ($str === '')
		{
			return '';
		}

				if (strpos($str, "\r") !== FALSE)
		{
			$str = str_replace(array("\r\n", "\r"), "\n", $str);
		}

						if ($reduce_linebreaks === TRUE)
		{
			$str = preg_replace("/\n\n+/", "\n\n", $str);
		}

				$html_comments = array();
		if (strpos($str, '<!--') !== FALSE && preg_match_all('#(<!\-\-.*?\-\->)#s', $str, $matches))
		{
			for ($i = 0, $total = count($matches[0]); $i < $total; $i++)
			{
				$html_comments[] = $matches[0][$i];
				$str = str_replace($matches[0][$i], '{@HC'.$i.'}', $str);
			}
		}

						if (strpos($str, '<pre') !== FALSE)
		{
			$str = preg_replace_callback('#<pre.*?>.*?</pre>#si', array($this, '_protect_characters'), $str);
		}

				$str = preg_replace_callback('#<.+?>#si', array($this, '_protect_characters'), $str);

				if ($this->protect_braced_quotes === TRUE)
		{
			$str = preg_replace_callback('#\{.+?\}#si', array($this, '_protect_characters'), $str);
		}

								$str = preg_replace('#<(/*)('.$this->inline_elements.')([ >])#i', '{@TAG}\\1\\2\\3', $str);

		
		$chunks = preg_split('/(<(?:[^<>]+(?:"[^"]*"|\'[^\']*\')?)+>)/', $str, -1, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);

				$str = '';
		$process = TRUE;

		for ($i = 0, $c = count($chunks) - 1; $i <= $c; $i++)
		{
									if (preg_match('#<(/*)('.$this->block_elements.').*?>#', $chunks[$i], $match))
			{
				if (preg_match('#'.$this->skip_elements.'#', $match[2]))
				{
					$process = ($match[1] === '/');
				}

				if ($match[1] === '')
				{
					$this->last_block_element = $match[2];
				}

				$str .= $chunks[$i];
				continue;
			}

			if ($process === FALSE)
			{
				$str .= $chunks[$i];
				continue;
			}

						if ($i === $c)
			{
				$chunks[$i] .= "\n";
			}

						$str .= $this->_format_newlines($chunks[$i]);
		}

				if ( ! preg_match('/^\s*<(?:'.$this->block_elements.')/i', $str))
		{
			$str = preg_replace('/^(.*?)<('.$this->block_elements.')/i', '<p>$1</p><$2', $str);
		}

				$str = $this->format_characters($str);

				for ($i = 0, $total = count($html_comments); $i < $total; $i++)
		{
												$str = preg_replace('#(?(?=<p>\{@HC'.$i.'\})<p>\{@HC'.$i.'\}(\s*</p>)|\{@HC'.$i.'\})#s', $html_comments[$i], $str);
		}

				$table = array(

																		'/(<p[^>*?]>)<p>/'	=> '$1', 
												'#(</p>)+#'			=> '</p>',
						'/(<p>\W*<p>)+/'	=> '<p>',

												'#<p></p><('.$this->block_elements.')#'	=> '<$1',

												'#(&nbsp;\s*)+<('.$this->block_elements.')#'	=> '  <$2',

												'/\{@TAG\}/'		=> '<',
						'/\{@DQ\}/'			=> '"',
						'/\{@SQ\}/'			=> "'",
						'/\{@DD\}/'			=> '--',
						'/\{@NBS\}/'		=> '  ',

																														"/><p>\n/"			=> ">\n<p>",

																		'#</p></#'			=> "</p>\n</"
						);

				if ($reduce_linebreaks === TRUE)
		{
			$table['#<p>\n*</p>#'] = '';
		}
		else
		{
									$table['#<p></p>#'] = '<p>&nbsp;</p>';
		}

		return preg_replace(array_keys($table), $table, $str);

	}

	
	/**
	 * Format Characters
	 *
	 * This function mainly converts double and single quotes
	 * to curly entities, but it also converts em-dashes,
	 * double spaces, and ampersands
	 *
	 * @param	string
	 * @return	string
	 */
	public function format_characters($str)
	{
		static $table;

		if ( ! isset($table))
		{
			$table = array(
																																																								'/\'"(\s|$)/'					=> '&#8217;&#8221;$1',
							'/(^|\s|<p>)\'"/'				=> '$1&#8216;&#8220;',
							'/\'"(\W)/'						=> '&#8217;&#8221;$1',
							'/(\W)\'"/'						=> '$1&#8216;&#8220;',
							'/"\'(\s|$)/'					=> '&#8221;&#8217;$1',
							'/(^|\s|<p>)"\'/'				=> '$1&#8220;&#8216;',
							'/"\'(\W)/'						=> '&#8221;&#8217;$1',
							'/(\W)"\'/'						=> '$1&#8220;&#8216;',

														'/\'(\s|$)/'					=> '&#8217;$1',
							'/(^|\s|<p>)\'/'				=> '$1&#8216;',
							'/\'(\W)/'						=> '&#8217;$1',
							'/(\W)\'/'						=> '$1&#8216;',

														'/"(\s|$)/'						=> '&#8221;$1',
							'/(^|\s|<p>)"/'					=> '$1&#8220;',
							'/"(\W)/'						=> '&#8221;$1',
							'/(\W)"/'						=> '$1&#8220;',

														"/(\w)'(\w)/"					=> '$1&#8217;$2',

														'/\s?\-\-\s?/'					=> '&#8212;',
							'/(\w)\.{3}/'					=> '$1&#8230;',

														'/(\W)  /'						=> '$1&nbsp; ',

														'/&(?!#?[a-zA-Z0-9]{2,};)/'		=> '&amp;'
						);
		}

		return preg_replace(array_keys($table), $table, $str);
	}

	
	/**
	 * Format Newlines
	 *
	 * Converts newline characters into either <p> tags or <br />
	 *
	 * @param	string
	 * @return	string
	 */
	protected function _format_newlines($str)
	{
		if ($str === '' OR (strpos($str, "\n") === FALSE && ! in_array($this->last_block_element, $this->inner_block_required)))
		{
			return $str;
		}

				$str = str_replace("\n\n", "</p>\n\n<p>", $str);

				$str = preg_replace("/([^\n])(\n)([^\n])/", '\\1<br />\\2\\3', $str);

				if ($str !== "\n")
		{
												$str =  '<p>'.rtrim($str).'</p>';
		}

						return preg_replace('/<p><\/p>(.*)/', '\\1', $str, 1);
	}

	
	/**
	 * Protect Characters
	 *
	 * Protects special characters from being formatted later
	 * We don't want quotes converted within tags so we'll temporarily convert them to {@DQ} and {@SQ}
	 * and we don't want double dashes converted to emdash entities, so they are marked with {@DD}
	 * likewise double spaces are converted to {@NBS} to prevent entity conversion
	 *
	 * @param	array
	 * @return	string
	 */
	protected function _protect_characters($match)
	{
		return str_replace(array("'",'"','--','  '), array('{@SQ}', '{@DQ}', '{@DD}', '{@NBS}'), $match[0]);
	}

	
	/**
	 * Convert newlines to HTML line breaks except within PRE tags
	 *
	 * @param	string
	 * @return	string
	 */
	public function nl2br_except_pre($str)
	{
		$newstr = '';
		for ($ex = explode('pre>', $str), $ct = count($ex), $i = 0; $i < $ct; $i++)
		{
			$newstr .= (($i % 2) === 0) ? nl2br($ex[$i]) : $ex[$i];
			if ($ct - 1 !== $i)
			{
				$newstr .= 'pre>';
			}
		}

		return $newstr;
	}

}
