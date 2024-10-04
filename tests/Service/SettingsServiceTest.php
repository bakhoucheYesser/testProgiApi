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
        // Create a mock of the SettingsRepository
        $this->settingsRepository = $this->createMock(SettingsRepository::class);

        // Instantiate the service with the mocked repository
        $this->settingsService = new SettingsService($this->settingsRepository);
    }

    public function testUpdateSettingCreatesNewSetting(): void
    {
        // Mock that no setting is found initially
        $this->settingsRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['settingKey' => 'test_key'])
            ->willReturn(null);

        // Expect the save method to be called once
        $this->settingsRepository->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Settings::class));

        // Call the method to test
        $setting = $this->settingsService->updateSetting('test_key', 42.0);

        // Assert that the new setting was created with the correct values
        $this->assertInstanceOf(Settings::class, $setting);
        $this->assertSame('test_key', $setting->getSettingKey());
        $this->assertSame(42.0, $setting->getValue());
    }

    public function testUpdateSettingUpdatesExistingSetting(): void
    {
        // Create a mock Settings entity to return from the repository
        $existingSetting = $this->createMock(Settings::class);

        // Mock the repository to return the existing setting
        $this->settingsRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['settingKey' => 'test_key'])
            ->willReturn($existingSetting);

        // Expect the save method to be called once
        $this->settingsRepository->expects($this->once())
            ->method('save')
            ->with($existingSetting);

        // Expect the setValue method to be called on the existing setting
        $existingSetting->expects($this->once())
            ->method('setValue')
            ->with(42.0);

        // Call the method to test
        $setting = $this->settingsService->updateSetting('test_key', 42.0);

        // Assert that the same setting is returned
        $this->assertSame($existingSetting, $setting);
    }
}
