<?php

/**
 * Test: Fregis\Events\DI\EventsExtension.
 *
 * @testCase Fregis\Events\DI\EventsExtensionTest
 * @author Karel Hák <karel.hak@fregis.cz>
 * @author Filip Procházka <filip@prochazka.su>
 * @package Fregis\Events
 */

namespace FregisTests\Events;

use Fregis;
use Kdyby;
use Nette;
use Tester;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/mocks.php';



/**
 * @author Filip Procházka <filip@prochazka.su>
 */
class ExtensionTest extends Tester\TestCase
{

	/**
	 * @param string $configFile
	 * @return \SystemContainer|\Nette\DI\Container
	 */
	public function createContainer($configFile)
	{
		$config = new Nette\Configurator();
		$config->setTempDirectory(TEMP_DIR);
		$config->addParameters(array('container' => array('class' => 'SystemContainer_' . md5($configFile))));
		Fregis\Events\DI\EventsExtension::register($config);
		$config->addConfig(__DIR__ . '/config/' . $configFile . '.neon');
		return $config->createContainer();
	}



	public function testInherited()
	{
		$container = $this->createContainer('inherited');

		$leafObject = $container->getService('leaf');
		/** @var LeafClass $leafObject */
		Assert::true($leafObject->onCreate instanceof Kdyby\Events\Event);
		Assert::same('FregisTests\Events\ParentClass::onCreate', $leafObject->onCreate->getName());

		$subscriber = $container->getService('subscriber');
		/** @var InheritSubscriber $subscriber */
		$leafObject->create();
		Assert::true(isset($subscriber->eventCalls['FregisTests\Events\ParentClass::onCreate']));
		Assert::equal(1, $subscriber->eventCalls['FregisTests\Events\ParentClass::onCreate']);

		Assert::true(isset($subscriber->eventCalls['FregisTests\Events\LeafClass::onCreate']));
		Assert::equal(1, $subscriber->eventCalls['FregisTests\Events\LeafClass::onCreate']);

		// not subscribed for middle class
		Assert::false(isset($subscriber->eventCalls['FregisTests\Events\InheritedClass::onCreate']));
	}

}

\run(new ExtensionTest());
