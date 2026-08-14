<?php
namespace App\Http\Controllers\Settings;

use App\Application\Services\Settings\SettingsService;
use App\Http\Controllers\Controller;
use App\Application\Services\Image\ImageService;;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SettingsController extends Controller
{
  public function __construct(
  private SettingsService $service,
  private ImageService $imageService
  ) {}

  public function index()
  {
    return response()->json($this->service->getAll());
  }

  public function updateGeneral(Request $request)
  {

   $this->ensureAllowed($request, [
      'company_name',
      'logo_light',
      'logo_dark',
      'theme'
   ]);

    $validated = $request->validate([
    'company_name' => 'sometimes|required|string',
    'logo_light' => 'sometimes|nullable|image',
    'logo_dark' => 'sometimes|nullable|image',
    'theme' => 'sometimes|required|in:light,dark'
    ]);

    $settings = $this->service->getGeneral();

    // if($request->hasFile('logo_light')){
    //   $validated['logo_light'] = $this->imageService->update(
    //     $request->file('logo_light'),
    //     $settings->logo_light,
    //     'settings/logos'
    //   );
    // }

    $this->handleImageField($request, $validated, 'logo_light', $settings->logo_light, 'settings/logos');

    // if($request->hasFile('logo_dark')){
    //   $validated['logo_dark'] = $this->imageService->update(
    //     $request->file('logo_dark'),
    //     $settings->logo_dark,
    //     'settings/logos'
    //   );
    // }

    $this->handleImageField($request, $validated, 'logo_dark', $settings->logo_dark, 'settings/logos');

    return response()->json(
      $this->service->updateGeneral($validated)
    );
  }

  public function updateContact(Request $request)
  {

   $this->ensureAllowed($request, [
      'phone',
      'email',
      'address',
      'business_hours',
      'social_links',
      'whatsapp_message',
      'show_in_footer',
      'show_contact_page',
      'map_url'
   ]);

    $validated = $request->validate([
      'phone' => 'sometimes|nullable|string',
      'email' => 'sometimes|nullable|email',
      'address' => 'sometimes|nullable|string',
      'business_hours' => 'sometimes|nullable|array',
      'social_links' => 'sometimes|nullable|array',
      'whatsapp_message' => 'sometimes|nullable|string',
      'show_in_footer' => 'sometimes|boolean',
      'show_contact_page' => 'sometimes|boolean',
      'map_url' => 'sometimes|nullable|string'
    ]);

    return response()->json(
      $this->service->updateContact($validated)
    );
  }

  public function updateChatbot(Request $request)
  {

   $this->ensureAllowed($request, [
      'enabled',
      'primary_color',
      'secondary_color',
      'icon',
      'position',
      'welcome_message',
      'show_delay_seconds',
      'auto_close_seconds'
   ]);

    $validated = $request->validate([
      'enabled' => 'sometimes|required|boolean',
      'primary_color' => ['sometimes','required','regex:/^#[0-9A-Fa-f]{6}$/'],
      'secondary_color' => ['sometimes','nullable','regex:/^#[0-9A-Fa-f]{6}$/'],
      'icon' => 'sometimes|nullable|image',
      'position' => 'sometimes|required|in:bottom-right,bottom-left',
      'welcome_message' => 'sometimes|nullable|string',
      'show_delay_seconds' => 'sometimes|integer|min:0',
      'auto_close_seconds' => 'sometimes|nullable|integer|min:0'
    ]);

    if (!array_key_exists('auto_close_seconds', $request->all())) {
      $validated['auto_close_seconds'] = null;
    }

    $settings = $this->service->getChatbot();

    // if($request->hasFile('icon')){
    //   $validated['icon'] = $this->imageService->update(
    //     $request->file('icon'),
    //     $settings->icon,
    //     'settings/chatbot'
    //   );
    // }

    $this->handleImageField($request, $validated, 'icon', $settings->icon, 'settings/chatbot');

    return response()->json(
    $this->service->updateChatbot($validated)
    );
  }

  // Bloquea campos no permitidos
  private function ensureAllowed(Request $request, array $allowed):void
  {
    $frameworkFields = ['_method', '_token'];
    $keys = array_merge(
           array_keys($request->all()),
           array_keys($request->allFiles())
       );
   // Quitar campos internos
    $keys = array_diff($keys, $frameworkFields);
    $unknown = array_diff($keys, $allowed);

    if(!empty($unknown)){
      throw new ValidationException(
      validator([], []),
      response()->json(
      [
        'error' => 'Campos no permitidos',
        'fields' => array_values($unknown)
      ], 422)
      );
    }
  }

  private function handleImageField(Request $request, array &$validated, string $field, ?string $current, string $path):void {
    if ($request->hasFile($field)){
      $validated[$field] = $this->imageService->update(
        $request->file($field),
        $current,
        $path
      );
    }elseif ($request->has($field) && $request->input($field) === null){
      if($current){
        $this->imageService->remove($current);
      }
      $validated[$field] = null;
    }
  }
}
