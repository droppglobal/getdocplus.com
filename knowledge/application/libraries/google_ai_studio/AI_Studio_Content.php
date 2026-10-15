<?php
class AI_Studio_Content {
	/** @var Ai_Studio_Part[] */
	public $parts = [];
	public $role='user';


	public function __construct() {
		$this->parts = [];
	}

	/**
	 * @return Ai_Studio_Part[]|array
	 */
	public function get_parts(): array {
		return $this->parts;
	}

	/**
	 * @param Ai_Studio_Part
	 */
	public function add_part( $part_text ) {
		$part = new Ai_Studio_Part();
		$part->set_text( $part_text );
		$this->parts[] = $part;

	}
}
