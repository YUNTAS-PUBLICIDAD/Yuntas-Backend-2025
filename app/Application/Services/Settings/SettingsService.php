<?php

namespace App\Application\Services\Settings;

use App\Models\ChatbotSetting;
use App\Models\ContactSetting;
use App\Models\GeneralSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
    if (isset($data['map_url'])) {
      $data['map_url'] = $this->normalizeMapUrl($data['map_url']);
    }

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

  private function normalizeMapUrl(?string $rawUrl): ?string
  {
    if (empty($rawUrl)) {
      return null;
    }

    $url = trim($rawUrl);

    // 0. Seguridad: Si no es de Google Maps ni iframe, no procesar
    $isGoogleMaps = str_contains($url, 'google.com/maps')
      || str_contains($url, 'maps.google.com')
      || str_contains($url, 'maps.app.goo.gl')
      || str_contains($url, 'goo.gl/maps')
      || str_contains($url, '<iframe');

    if (!$isGoogleMaps) {
      return null;
    }

    // 1. Si pegaron el iframe completo, extraer el atributo src
    if (str_contains($url, '<iframe') && preg_match('/src="([^"]+)"/', $url, $matches)) {
      $url = $matches[1];
    }

    // 2. Si es link acortado, resolverlo ultrarrápido con HEAD (~300ms)
    if (str_contains($url, 'maps.app.goo.gl') || str_contains($url, 'goo.gl/maps')) {
      try {
        $response = Http::withoutVerifying()->withoutRedirecting()->timeout(4)->head($url);
        $location = $response->header('Location');

        if (!empty($location)) {
          $url = $location;
        } else {
          // Fallback con GET solo si HEAD no devolvió la cabecera Location
          $response = Http::withoutVerifying()->timeout(5)->get($url);
          $effectiveUri = (string) $response->effectiveUri();
          if (!empty($effectiveUri)) {
            $url = $effectiveUri;
          }
        }
      } catch (\Throwable $e) {
        Log::warning("No se pudo resolver el enlace corto de Google Maps: {$url}. Error: " . $e->getMessage());
      }
    }

    // 3. Si ya es embed directo, devolverlo
    if (str_contains($url, '/embed') || str_contains($url, 'output=embed')) {
      return $url;
    }

    // 4. Si es una URL de lugar (/place/Nombre)
    if (preg_match('#/place/([^/]+)#', $url, $matches)) {
      return "https://www.google.com/maps?q={$matches[1]}&output=embed";
    }

    // 5. Si tiene coordenadas en la URL (@lat,lng)
    if (preg_match('/@(-?\d+\.\d+),(-?\d+\.\d+)/', $url, $matches)) {
      return "https://www.google.com/maps?q={$matches[1]},{$matches[2]}&output=embed";
    }

    // 6. Si tiene identificador cid
    if (str_contains($url, 'cid=') && preg_match('/[?&]cid=([^&]+)/', $url, $matches)) {
      return "https://maps.google.com/maps?cid={$matches[1]}&output=embed";
    }

    // 7. Si tiene parámetro q=
    $parsed = parse_url($url);
    if (isset($parsed['query'])) {
      parse_str($parsed['query'], $query);
      if (!empty($query['q'])) {
        $q = urlencode($query['q']);
        return "https://www.google.com/maps?q={$q}&output=embed";
      }
    }

    // Filtro final: Si no es un embed válido, devolver null
    if (!str_contains($url, '/embed') && !str_contains($url, 'output=embed')) {
      return null;
    }

    return $url;
  }
}
