<?php

namespace King23\Settings;

class EnvSettingsFactory
{
    protected $settings = [];

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

        foreach($this->settings[$environment] as $file) {
            $settingsChain->registerSettingsProvider(JsonSettings::fromFilename($file));
        }

        return $settingsChain;
    }
}
