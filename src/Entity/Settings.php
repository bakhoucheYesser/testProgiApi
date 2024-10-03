<?php

namespace App\Entity;

use App\Repository\SettingsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SettingsRepository::class)]
class Settings extends BaseEntity
{

    #[ORM\Column(length: 255)]
    private ?string $setting_key = null;

    #[ORM\Column]
    private ?float $value = null;

    public function getSettingKey(): ?string
    {
        return $this->setting_key;
    }

    public function setSettingKey(string $setting_key): static
    {
        $this->setting_key = $setting_key;

        return $this;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(float $value): static
    {
        $this->value = $value;

        return $this;
    }
}
