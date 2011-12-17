<?php

namespace AmuSilexExtension\SilexConfig;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\Yaml\Yaml;

class YamlConfig implements ServiceProviderInterface
{
    private $filename;

    private $replacements = array();

    public function __construct($filename, array $replacements = array())
    {
        $this->filename = $filename;

        if ($replacements) {
            foreach ($replacements as $key => $value) {
                $this->replacements['%'.$key.'%'] = $value;
            }
        }
    }

    public function register(Application $app)
    {
        if (!file_exists($this->filename)) {
            throw new \InvalidArgumentException(
                sprintf("The config file '%s' does not exist.", $this->filename));
        }

        $config = Yaml::parse($this->filename);

        if (null === $config) {
            throw new \InvalidArgumentException(
                sprintf("The config file '%s' appears to be invalid YAML.", $this->filename));
        }

		$replacedConfig = array();

        foreach ($config as $name => $value) {
            $replacedConfig[$name] = $this->doReplacements($value);
        }
		$app['config'] = $replacedConfig;
    }

    private function doReplacements($value)
    {
        if (!$this->replacements) {
            return $value;
        }

        if (is_array($value)) {
            foreach ($value as $k => $v) {
                $value[$k] = $this->doReplacements($v);
            }

            return $value;
        }

        return strtr($value, $this->replacements);
    }
}
