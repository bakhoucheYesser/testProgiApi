<?php

namespace App\Service;

use App\Repository\SettingsRepository;
use App\Entity\Settings;

class SettingsService
{
    private $settingsRepository;

    public function __construct(SettingsRepository $settingsRepository)
    {
        $this->settingsRepository = $settingsRepository;
    }

    /**
     * Update or create a setting in the database.
     */
    public function updateSetting(string $key, float $value): Settings
    {
        $setting = $this->settingsRepository->findOneBy(['settingKey' => $key]);

        if (!$setting) {
            $setting = new Settings();
            $setting->setSettingKey($key);
        }

        $setting->setValue($value);
        $this->settingsRepository->save($setting);

        return $setting;
    }
}

