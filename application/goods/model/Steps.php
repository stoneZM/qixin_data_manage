<?php
namespace app\goods\model;

class Steps {
	private $all;
	private $count;
	private $curr;
	function __construct() {
		$this->count = 0;
	}

	function add($step) {
		$this->count++;
		$this->all[$this->count] = $step;
	}

	function setCurrent($step) {
		reset($this->all);
		for ($i = 1; $i <= $this->count; $i++) {
			if ($this->all[$i] == $step)
			break;
			next($this->all);
		}
		$this->curr = current($this->all);
	}
	function getAll() {
		
		return $this->all;
		
	}
	function getCurrent() {
		return $this->curr;
	}
	
	function getNext() {
		self::setCurrent($this->curr);
		return prev($this->all);
	}
	
	function getPrev() {
		self::setCurrent($this->curr);
		return next($this->all);
	}

}