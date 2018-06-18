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
use Nette\PhpGenerator as Code;

/**
 * @author Karel Hák <karel.hak@fregis.cz>
 */
class EventsExtension extends Events\DI\EventsExtension
{
	protected function bindEventProperties(Nette\DI\ServiceDefinition $def, Nette\Reflection\ClassType $class)
	{		
		foreach ($class->getProperties(Nette\Reflection\Property::IS_PUBLIC) as $property) {
			if (!preg_match('#^on[A-Z]#', $name = $property->getName())) {
				continue 1;
			}

			$currentClass = $class;
			$declaringClassName = $property->getDeclaringClass()->getName();
			do {
				$currentClassName = $currentClass->getName();
				if(!$currentClass->isAbstract()) {
					$def->addSetup('$' . $name, array(
						new Nette\DI\Statement($this->prefix('@manager') . '::createEvent', array(
							array($currentClassName, $name),
							new Code\PhpLiteral('$service->' . $name)
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
