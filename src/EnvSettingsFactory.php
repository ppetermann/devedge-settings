<?php

namespace Devedge\Settings;

use King23\Settings\JsonSettings;
use King23\Settings\SettingsChain;

class EnvSettingsFactory
{
    protected $settings = [];
    protected $home = "./";

    /**
     * @param string $home
     * @return self $this
     */
    public function setHome(string $home)
    {
        $this->home = $home;
        return $this;
    }

    /**
     * @param string $filename
     * @return EnvSettingsFactory
     * @throws \Exception
     */
    public function setEnvironmentsFromJsonFile(string $filename): EnvSettingsFactory
    {
        if (!file_exists($filename)) {
            throw new \Exception("file '$filename' not found");
        }
        if (!defined("JSON_THROW_ON_ERROR")) {
            define("JSON_THROW_ON_ERROR", 4194304);
        }

        $this->settings = json_decode(file_get_contents($filename), true, 512, JSON_THROW_ON_ERROR);
        return $this;
    }

    /**
     * @param string $environment
     * @param array $files
     * @return EnvSettingsFactory
     */
    public function registerEnvironmentFiles(string $environment, array $files): EnvSettingsFactory
    {
        $this->settings[$environment] = $files;
        return $this;
    }

    /**
     * @param string $environment
     * @return SettingsChain
     * @throws \Exception
     */
    public function getSettingsChainFor(string $environment): SettingsChain
    {
        if (!isset($this->settings[$environment])) {
            throw new \Exception("no settings for environment: '$environment' known.");
        }

        $settingsChain = new SettingsChain();

        foreach ($this->settings[$environment] as $file) {
            if(substr($file, 0, 2) == "~/") {
                $file = $this->home . substr($file, 2);
            }
            if (!file_exists($file)) {
                continue;
            }
            $settingsChain->registerSettingsProvider(JsonSettings::fromFilename($file));
        }
        return $settingsChain;
    }
}
