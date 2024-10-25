<?php

namespace App\Tests\Service;

use App\Entity\Settings;
use App\Repository\SettingsRepository;
use App\Service\SettingsService;
use PHPUnit\Framework\TestCase;

class SettingsServiceTest extends TestCase
{
    private $settingsRepository;
    private $settingsService;

    protected function setUp(): void
    {
        $this->settingsRepository = $this->createMock(SettingsRepository::class);

        $this->settingsService = new SettingsService($this->settingsRepository);
    }

    public function testUpdateSettingCreatesNewSetting(): void
    {
        $this->settingsRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['settingKey' => 'test_key'])
            ->willReturn(null);

        $this->settingsRepository->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Settings::class));

        $setting = $this->settingsService->updateSetting('test_key', 42.0);

        $this->assertInstanceOf(Settings::class, $setting);
        $this->assertSame('test_key', $setting->getSettingKey());
        $this->assertSame(42.0, $setting->getValue());
    }

    public function testUpdateSettingUpdatesExistingSetting(): void
    {
        $existingSetting = $this->createMock(Settings::class);

        $this->settingsRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['settingKey' => 'test_key'])
            ->willReturn($existingSetting);

        $this->settingsRepository->expects($this->once())
            ->method('save')
            ->with($existingSetting);

        $existingSetting->expects($this->once())
            ->method('setValue')
            ->with(42.0);

        $setting = $this->settingsService->updateSetting('test_key', 42.0);

        $this->assertSame($existingSetting, $setting);
    }
}
