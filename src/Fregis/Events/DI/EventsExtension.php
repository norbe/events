<?php

/********************************************************************\
/*  This file is part of the Fregis System (http://www.fregis.cz)     \
/*  Copyright (c) 2012 Karel Hák, Martin Jelič, Jakub Kocourek         \
/*                                                                     /
/*  @license http://www.fregis.cz/license_public                      /
/********************************************************************/

namespace Fregis\Events\DI;

use Kdyby\Events;
use Nette;
use Nette\DI\Definitions\FactoryDefinition;
use Nette\DI\Definitions\Statement;

/**
 * @author Karel Hák <karel.hak@fregis.cz>
 */
class EventsExtension extends Events\DI\EventsExtension
{
	protected function bindEventProperties(Nette\DI\Definitions\Definition $def, \ReflectionClass $class)
	{
		/** @var \Nette\DI\Definitions\ServiceDefinition $def */
		$def = $def instanceof FactoryDefinition ? $def->getResultDefinition() : $def;
		foreach ($class->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
			if (!preg_match('#^on[A-Z]#', $name = $property->getName())) {
				continue 1;
			}

			$currentClass = $class;
			$declaringClassName = $property->getDeclaringClass()->getName();
			do {
				$currentClassName = $currentClass->getName();
				if(!$currentClass->isAbstract()) {
					$def->addSetup('$' . $name . '[]', array(
						new Statement($this->prefix('@manager') . '::createEvent', array(
							array($currentClassName, $name)
						))
					));
				}
				$currentClass = $currentClass->getParentClass();
			} while ($declaringClassName != $currentClassName);
		}
	}

	/**
	 * @param \Nette\Configurator $configurator
	 */
	public static function register(Nette\Configurator $configurator)
	{
		$configurator->onCompile[] = function ($config, Nette\DI\Compiler $compiler) {
			$compiler->addExtension('events', new EventsExtension());
		};
	}
}
