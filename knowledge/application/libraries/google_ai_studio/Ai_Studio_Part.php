<?php
/**
 * Ai_Studio_Parts
 *
 * @author: appsbd
 *
 */


class Ai_Studio_Part {
	public $text;

	public function set_text( $text ,$role='user') {
		$this->text = $text;
	}
}