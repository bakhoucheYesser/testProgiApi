<?php

namespace App\Controller\settings;

use App\DTO\SettingsDTO;
use App\Helper\RequestHelper;
use App\Service\SettingsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class EditController extends AbstractController
{
    private $requestHelper;
    private $settingsService;

    public function __construct(RequestHelper $requestHelper, SettingsService $settingsService)
    {
        $this->requestHelper = $requestHelper;
        $this->settingsService = $settingsService;
    }


    #[IsGranted("ROLE_ADMIN")]
    #[Route('/api/settings', name: 'update_setting', methods: ['PUT'])]
    public function updateSetting(Request $request): JsonResponse
    {

        $data = json_decode($request->getContent(), true);

        $dto = new SettingsDTO($data['settingKey'], $data['value']);
        $errors = $this->requestHelper->validateDTO($dto);
        if (count($errors) > 0) {
            return $this->requestHelper->formatValidationErrors($errors);
        }

        $setting = $this->settingsService->updateSetting(
            $dto->getSettingKey(),
            $dto->getValue()
        );

        return $this->json([
            'status' => 'Setting updated!',
            'setting' => [
                'key' => $setting->getSettingKey(),
                'value' => $setting->getValue()
            ]
        ]);
    }
}
