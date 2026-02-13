<?php

namespace App\Traits;

trait ValidatesImageSecurity
{
    private function validateImageSecurity($file): void
    {
        // Validar tipo MIME real
        $allowedMimes = ['image/webp'];
        $realMime = mime_content_type($file->getPathname());
        
        if (!in_array($realMime, $allowedMimes)) {
            throw new \InvalidArgumentException('Tipo de archivo no permitido. Solo imágenes válidas.');
        }

        // Validar que realmente es una imagen
        try {
            $imageInfo = getimagesize($file->getPathname());
            if ($imageInfo === false) {
                throw new \InvalidArgumentException('El archivo no es una imagen válida.');
            }
        } catch (\Exception $e) {
            throw new \InvalidArgumentException('Archivo de imagen corrupto o inválido.');
        }

        // Validar tamaño
        if ($file->getSize() > 2097152) { //2MB
            throw new \InvalidArgumentException('La imagen es demasiado grande. 2MB.');
        }

        // Validar dimensiones máximas
        if ($imageInfo[0] > 4000 || $imageInfo[1] > 4000) {
            throw new \InvalidArgumentException('Dimensiones de imagen demasiado grandes. Máximo 4000x4000px.');
        }
    }
}