<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
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

        // Escanear contenido en busca de código malicioso
        $this->scanForMaliciousContent($file->getPathname());

        // Validar encabezados específicos 
        $this->validateWebPHeader($file->getPathname());
    }

    /**
     * Escanea TODO el archivo por contenido malicioso
     */
    private function scanForMaliciousContent(string $filePath): void
    {
        $suspiciousPatterns = [
            '<?php', '<?=',
            '<script', '</script>',
            'eval(', 'exec(', 'system(', 'shell_exec(', 'passthru(',
            'file_get_contents(', 'file_put_contents(', 'fopen(', 'fwrite(',
            'base64_decode(', 'base64_encode(',
            'curl_exec(', 'wget ', 'chmod ', 
            '__halt_compiler', 'create_function',
            'preg_replace.*e[\'"]',
            'assert(', 'call_user_func',
            'include(', 'require(', 'include_once(', 'require_once(',
            '/bin/sh', '/bin/bash', 'cmd.exe',
            'javascript:', 'vbscript:',
            'onload=', 'onerror=', 'onclick=',
            'document.cookie', 'document.write',
            '<iframe', '<object', '<embed',
            'FSO.CreateTextFile',
            'WScript.Shell', 'ActiveXObject'
        ];

        $fileSize = filesize($filePath);
        $chunkSize = 1024 * 32;
        $handle = fopen($filePath, 'rb');
        
        if (!$handle) {
            throw new \InvalidArgumentException('No se puede leer el archivo para validación.');
        }

        $previousChunk = '';
        $position = 0;

        try {
            while (!feof($handle) && $position < $fileSize) {
                $currentChunk = fread($handle, $chunkSize);
                $searchContent = strtolower($previousChunk . $currentChunk);
                
                foreach ($suspiciousPatterns as $pattern) {
                    if (strpos($searchContent, strtolower($pattern)) !== false) {
                        Log::warning("Contenido malicioso detectado: {$pattern} en archivo", [
                            'file' => basename($filePath),
                            'position' => $position,
                            'pattern' => $pattern
                        ]);
                        throw new \InvalidArgumentException("Archivo contiene contenido malicioso.");
                    }
                }

                $previousChunk = substr($currentChunk, -512);
                $position += $chunkSize;
                
                if ($position > 10 * 1024 * 1024) {
                    break;
                }
            }
        } finally {
            if (is_resource($handle)) {
                fclose($handle);
            }
        }
    }

    /**
     * Valida que el archivo tenga un header WebP válido y bien formado
     */
    private function validateWebPHeader(string $filePath): void
    {
        $handle = fopen($filePath, 'rb');
        if (!$handle) {
            throw new \InvalidArgumentException('No se puede abrir el archivo para validación.');
        }

        try {
            // Leer primeros 16 bytes del header WebP
            $header = fread($handle, 16);
            
            if (strlen($header) < 12) {
                throw new \InvalidArgumentException('Archivo WebP truncado o inválido.');
            }

            // WebP header structure:
            // Bytes 0-3: "RIFF"
            // Bytes 8-11: "WEBP"
            
            $riffSignature = substr($header, 0, 4);
            $webpSignature = substr($header, 8, 4);
            
            if ($riffSignature !== 'RIFF') {
                throw new \InvalidArgumentException('Header RIFF inválido en archivo WebP.');
            }
            
            if ($webpSignature !== 'WEBP') {
                throw new \InvalidArgumentException('Header WEBP inválido.');
            }

            // Validar tamaño declarado en header vs tamaño real
            $declaredSize = unpack('V', substr($header, 4, 4))[1];
            $realSize = filesize($filePath) - 8; // -8 porque el tamaño no incluye RIFF header
            
            // Permitir pequeñas diferencias (metadatos)
            if (abs($declaredSize - $realSize) > 1024) {
                throw new \InvalidArgumentException('Tamaño declarado en header WebP no coincide con tamaño real.');
            }

        } finally {
            fclose($handle);
        }
    }
}