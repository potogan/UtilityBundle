<?php
namespace Potogan\UtilityBundle\HostConfig;

use Symfony\Component\DependencyInjection\ContainerInterface;

class Service {
	protected $container;
	protected $config;

	public function __construct(ContainerInterface $container, array $config)
	{
		$this->container = $container;
		$this->config = $config;
	}

	public function get($name, $default = null)
	{
		foreach ($this->getHosts() as $h) {
			if (isset($this->config[$h]) && is_array($this->config[$h]) && array_key_exists($name, $this->config[$h])) {
				return $this->config[$h][$name];
			}
		}

		return $default;
	}

	public function has($name)
	{
		foreach ($this->getHosts() as $h) {
			if (isset($this->config[$h]) && is_array($this->config[$h]) && array_key_exists($name, $this->config[$h])) {
				return true;
			}
		}

		return false;
	}

	protected function getHosts()
	{
		$host = $this->container->get('request')->getHttpHost();
		$res = array();
		$cumulated = false;
		$parts = array_reverse(explode('.', $host));

		foreach ($parts as $part) {
			if ($cumulated) {
				$cumulated = $part . '.' . $cumulated;
			} else {
				$cumulated = $part;
			}

			$res[] = $cumulated;
		}

		$res = array_reverse($res);

		$res[] = '*';

		return $res;
	}
}