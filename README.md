Fregis/Events [![Build Status](https://secure.travis-ci.org/Kdyby/Events.png?branch=master)](http://travis-ci.org/Kdyby/Events)
===========================

What is that
------------

This is extension for [kdyby/events](https://github.com/kdyby/events) which allows you to 
subscription for events on children class.

Example:
For event Nette\UI\Presenter::onShutdown it allows subscribe for specific presenter like SomePresenter::onShutdow


Usage
------
For details see [kdyby/events](https://github.com/kdyby/events)

The only difference in usage is:
 - composer package is 'fregis/events' instead of 'kdyby/events'
 - nette extension class is 'Fregis\Events\DI\EventsExtension' instead of 'Kdyby\Events\DI\EventsExtension'