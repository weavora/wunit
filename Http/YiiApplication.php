<?php

class YiiApplication extends CWebApplication {


	public function end($status=0, $exit=true) {
		parent::end(0, false);
		throw new YiiExitException();
	}
}