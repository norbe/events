<?php

/**
 * This file is part of the Kdyby (http://www.kdyby.org)
 *
 * Copyright (c) 2008 Filip Procházka (filip@prochazka.su)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace FregisTests\Events;

use Kdyby;
use Nette;


class ParentClass extends Nette\Object
{
	public $onCreate;

	public function create() {
		$this->onCreate();
	}
}

class InheritedClass extends ParentClass
{
}

class LeafClass extends InheritedClass
{	
}

class InheritSubscriber implements Kdyby\Events\Subscriber
{
	public $eventCalls = array();

	/**
	 * @return array
	 */
	public function getSubscribedEvents()
	{
		return array(
			'FregisTests\Events\LeafClass::onCreate',
			'FregisTests\Events\ParentClass::onCreate',
		);
	}



	public function onCreate() {
		$backtrace = debug_backtrace();
		$event = $backtrace[2]['args'][0];
		$this->eventCalls[$event] = 1 + (isset($this->eventCalls[$event]) ? $this->eventCalls[$event] : 0);
	}

}