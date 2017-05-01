<?php

trait Error {
	public function error() {
		$error = "Error saving " . get_class($this) . PHP_EOL;
		
		foreach ($this->getMessages() as $message) {
			
			$error .= $message . PHP_EOL;			
		}
		
		throw new Exception($error);
		
	}
	
}
