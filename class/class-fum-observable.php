<?php
/**
 * From http://ken-soft.com/2011/11/20/php-observerobservable-design-pattern/
 */

class Fum_Observable {

	private $changed = false;

	/** @var Fum_Observer[] $observers */
	private $observers = array();

	public function addObserver( Fum_Observer $o ) {
		if ( $o == null ) {
			throw new Exception();
		}
		$contains = 0;
		foreach ( $this->observers as $observer ) {
			if ( $o === $observer ) {
				$contains = true;
				break;
			}
		}
		if ( ! $contains ) {
			$this->observers[] = $o;
		}
	}

	public function deleteObserver( Fum_Observer $o ) {
		for ( $i = 0; $i < count( $this->observers ); $i ++ ) {
			if ( $this->observers[$i] == $o ) {
				unset( $this->observers[$i] );
			}
		}
		$observers = array();
		foreach ( $this->observers as $observer ) {
			$observers[] = $observer;
		}
		$this->observers = $observers;
	}

	public function notifyObservers() {


		if ( $this->changed ) {
			foreach ( $this->observers as $ob ) {
				$ob->update( $this );
			}
		}

	}

	public
	function deleteObservers() {
		$this->observers = array();
	}

	protected
	function setChanged() {
		$this->changed = true;
	}

	protected
	function clearChanged() {
		$this->changed = false;
	}

	public
	function hasChanged() {
		return $this->changed;
	}

	public
	function countObservers() {
		return count( $this->observers );
	}

}

