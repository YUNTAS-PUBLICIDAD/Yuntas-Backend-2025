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
        $imageInfo = getimagesize($file->getPathname());
        if ($imageInfo === false) {
            throw new \InvalidArgumentException('El archivo no es una imagen válida.');
        }

        // Validar tamaño
        if ($file->getSize() > 2097152) { //2MB
            throw new \InvalidArgumentException('La imagen es demasiado grande. 2MB.');
        }

        // Validar dimensiones máximas
        if ($imageInfo[0] > 4000 || $imageInfo[1] > 4000) {
            throw new \InvalidArgumentException('Dimensiones de imagen demasiado grandes. Máximo 4000x4000px.');
        }

        // Detectar contenido malicioso básico
        $content = file_get_contents($file->getPathname(), false, null, 0, 1024);
        $suspiciousPatterns = ['<?php', '<?=', '<script', 'eval(', 'exec(', 'base64_decode(', 'shell_exec('];
        
        foreach ($suspiciousPatterns as $pattern) {
            if (stripos($content, $pattern) !== false) {
                throw new \InvalidArgumentException('Archivo contiene contenido malicioso.');
            }
        }
    }
}