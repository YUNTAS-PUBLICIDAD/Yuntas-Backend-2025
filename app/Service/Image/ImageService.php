<?php

namespace App\Service\Image;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
}