<?php

namespace App\DTO;

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class SettingsDTO
{
    #[Assert\NotBlank(message: "The setting key cannot be blank.")]
    #[Assert\Length(min: 3, minMessage: "The setting key must be at least 3 characters long.")]
    private $settingKey;

    #[Assert\NotBlank(message: "The setting value cannot be blank.")]
    #[Assert\Type(type: "numeric", message: "The setting value must be a number.")]
    private $value;

    public function __construct($settingKey, $value)
    {
        $this->settingKey = $settingKey;
        $this->value = $value;
    }

    public function getSettingKey(): string
    {
        return $this->settingKey;
    }

    public function getValue(): float
    {
        return $this->value;
    }
}
