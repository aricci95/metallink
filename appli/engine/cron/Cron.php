<?php
Abstract class Cron
{
	public $model;
	public $container;
	public $context;

    public function __construct()
    {
    	$this->context   = Context::getInstance();
        $this->model     = Model_Manager::getInstance();
        $this->container = Service_Container::getInstance();
    }

    public function get($service)
    {
        return $this->container->get($service);
    }
}