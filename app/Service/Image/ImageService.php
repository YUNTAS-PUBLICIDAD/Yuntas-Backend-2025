<?php

namespace App\Service\Image;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
// use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;
// use Intervention\Image\Laravel\Facades\Image;
use RuntimeException;
use Throwable;

class ImageService
{
  /**
   * @param UploadedFile $archivo
   * @param string $directorio
   * @param string $disco
   * @return string
   */
  public function guardarImagen(UploadedFile $archivo, string $directorio = 'imagenes', string $disco = 'public'): string
  {
    $nombre = $this->generarNombreUnico($archivo);
    $archivo->storeAs($directorio, $nombre, $disco);

    return "/storage/{$directorio}/{$nombre}";
  }

  /**
   * @param string $rutaImagen
   * @param string $disco
   * @return bool
   */
  public function eliminarImagen(string $rutaImagen, string $disco = 'public'): bool
  {
    $rutaRelativa = str_replace('/storage/', '', $rutaImagen);
    return Storage::disk($disco)->delete($rutaRelativa);
  }

  /**
   * @param array $rutasImagenes
   * @param string $disco
   * @return bool
   */
  public function eliminarImagenes(array $rutasImagenes, string $disco = 'public'): bool
  {
    $rutasRelativas = array_map(function ($ruta) {
      return str_replace('/storage/', '', $ruta);
    }, $rutasImagenes);

    return Storage::disk($disco)->delete($rutasRelativas);
  }

  /**
   * @param UploadedFile $nuevaImagen
   * @param string|null $imagenAnterior
   * @param string $directorio
   * @param string $disco
   * @return string
   */
  public function actualizarImagen(
    UploadedFile $nuevaImagen,
    ?string $imagenAnterior = null,
    string $directorio = 'imagenes',
    string $disco = 'public'
  ): string {
    if ($imagenAnterior) {
      $this->eliminarImagen($imagenAnterior, $disco);
    }

    return $this->guardarImagen($nuevaImagen, $directorio, $disco);
  }

  /**
   * @param UploadedFile $archivo
   * @return string
   */
  private function generarNombreUnico(UploadedFile $archivo): string
  {
    $extension = $archivo->getClientOriginalExtension();
    $uuid = Str::uuid();
    $timestamp = time();

    return "{$uuid}_{$timestamp}.{$extension}";
  }

  /**
   * @param UploadedFile $archivo
   * @param array $extensionesPermitidas
   * @return bool
   */
  public function esImagenValida(UploadedFile $archivo, array $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp']): bool
  {
    $extension = strtolower($archivo->getClientOriginalExtension());
    return in_array($extension, $extensionesPermitidas) && $archivo->isValid();
  }

  /**
   * @param UploadedFile $archivo
   * @return array
   */
  public function obtenerInfoImagen(UploadedFile $archivo): array
  {
    return [
      'nombre_original' => $archivo->getClientOriginalName(),
      'extension' => $archivo->getClientOriginalExtension(),
      'tamaño' => $archivo->getSize(),
      'tipo_mime' => $archivo->getMimeType(),
    ];
  }

  public function store(
    UploadedFile $file,
    string $directory = 'images',
    string $disk = 'public'
  ): string {
    if (! $file->isValid()) {
      throw new \RuntimeException('Invalid uploaded file');
    }

    $this->validateMime($file);

    // Validación crítica
    // if(!extension_loaded('gd')){
    //   throw new RuntimeException('Server misconfigured: GD extension required');
    // }
    // if(!extension_loaded('gd')){
    //   throw new RuntimeException('GD not installed');
    // }
    // $manager = new ImageManager(new Driver());
    // try {
    // $image = Image::decode($file)->scaleDown(width: 1200);
    // $image = $manager->read($file)
    //   ->resize(1200, null, function ($constraint){
    //     $constraint->aspectRatio();
    //     $constraint->upsize();
    //   });
    // $image = $manager->read($file)
    //   ->scaleDown(width: 1200);
    // } catch (Throwable $e) {
    //  throw new RuntimeException('Invalid image content');
    // }

    // $encoded = $image->encode(new WebpEncoder(quality: 80));
    // $encoded = $image->toWebp(80);

    // $extension = $file->extension();

    // $filename = Str::uuid().'_'.time().'.'.$extension;

    // $path = $file->storeAs($directory, $filename, $disk);

    $filename = Str::uuid().'_'.time().'.'.$file->getClientOriginalExtension();
    $path = $file->storeAs($directory, $filename, $disk);

    // Storage::disk($disk)->put($path, (string) $image);
    // Storage::disk($disk)->put($path, (string) $encoded);
    return Storage::url($path);
  }

  public function remove(string $imagePath, string $disk = 'public'): bool
  {
    // $relativePath = str_replace('/storage/', '', $imagePath);
    // $relativePath = ltrim(parse_url($imagePath, PHP_URL_PATH), '/storage/');
    // $relativePath = str_replace('/storage/', '', parse_url($imagePath, PHP_URL_PATH));
    $path = parse_url($imagePath, PHP_URL_PATH);
    if (! $path) {
      return false;
    }
    $relativePath = str_replace('/storage/', '', $path);

    if (! Storage::disk($disk)->exists($relativePath)) {
      return false;
    }
    return Storage::disk($disk)->delete($relativePath);
  }

  public function update(UploadedFile $file, ?string $oldImage = null, string $directory = 'images', string $disk = 'public'): string
  {
    if ($oldImage) {
      $this->remove($oldImage, $disk);
    }
    return $this->store($file, $directory, $disk);
  }

  private array $allowedMimeTypes = [
    'image/jpeg',
    'image/png',
    'image/webp',
    'image/svg+xml',
  ];

  private function validateMime(UploadedFile $file): void
  {
    if (! in_array($file->getMimeType(), $this->allowedMimeTypes, true)) {
      throw new \RuntimeException('Invalid image type');

    }
  }
}
