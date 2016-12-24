<?php
/**
 * From http://ken-soft.com/2011/11/20/php-observerobservable-design-pattern/
 */
interface Fum_Observer {
	public function update( Fum_Observable $o );
} 