<?php

namespace App\Application\Services\Settings;

use App\Models\ChatbotSetting;
use App\Models\ContactSetting;
use App\Models\GeneralSetting;

class SettingsService
{

  public function getAll(): array
  {
    return cache()->remember('settings.all', 3600, function (){
    return [
      'general' => GeneralSetting::first(),
      'contact' => ContactSetting::first(),
      'chatbot' => ChatbotSetting::first(),
    ];
    });
  }

  public function updateGeneral(array $data)
  {
    $settings = GeneralSetting::firstOrFail();
    $settings->update($data);

    $this->clearCache();
    return $settings;
  }

  public function updateContact(array $data)
  {
    $settings = ContactSetting::firstOrFail();
    $settings->update($data);

    $this->clearCache();
    return $settings;
  }

  public function updateChatbot(array $data)
  {
    $settings = ChatbotSetting::firstOrFail();
    $settings->update($data);

    $this->clearCache();
    return $settings;
  }

  public function getGeneral()
  {
    return GeneralSetting::firstOrFail();
  }

  public function getChatbot()
  {
    return ChatbotSetting::firstOrFail();
  }

  private function clearCache():void
  {
    cache()->forget('settings.all');
  }
}
