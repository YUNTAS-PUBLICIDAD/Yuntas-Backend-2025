<?php

namespace App\Traits;

trait SanitizesInput
{
    /**
     * Sanitiza campos de texto libre eliminando HTML y caracteres peligrosos
     */
    private function sanitizeText(?string $text): ?string
    {
        if (!$text) return $text;

        // Eliminar tags HTML
        $text = strip_tags($text);
        
        // Convertir caracteres especiales
        $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        
        // Eliminar caracteres de control
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $text);
        
        // Limpiar espacios
        $text = trim($text);
        
        return $text;
    }

    /**
     * Sanitiza contenido HTML permitiendo solo tags básicos seguros
     */
    private function sanitizeHtml(?string $html): ?string
    {
        if (!$html) return $html;

        // Lista de tags HTML permitidos (básicos para descripción)
        $allowedTags = '<p><br><strong><b><em><i><u><ul><ol><li><h3><h4>';
        
        // Eliminar tags no permitidos
        $html = strip_tags($html, $allowedTags);
        
        // Eliminar atributos peligrosos (onclick, onload, etc.)
        $html = preg_replace('/\s*on\w+\s*=\s*["\'][^"\']*["\']/', '', $html);
        
        // Eliminar javascript: y data: protocols
        $html = preg_replace('/javascript\s*:/i', '', $html);
        $html = preg_replace('/data\s*:/i', '', $html);
        
        // Eliminar style attributes
        $html = preg_replace('/\s*style\s*=\s*["\'][^"\']*["\']/', '', $html);
        
        return trim($html);
    }

    /**
     * Sanitiza keywords separadas por comas
     */
    private function sanitizeKeywords(?string $keywords): ?string
    {
        if (!$keywords) return $keywords;

        // Convertir a texto plano
        $keywords = strip_tags($keywords);
        
        // Separar por comas y limpiar cada keyword
        $keywordArray = explode(',', $keywords);
        $cleanKeywords = [];
        
        foreach ($keywordArray as $keyword) {
            $clean = trim(htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8'));
            if (!empty($clean) && strlen($clean) <= 50) { // Limitar longitud
                $cleanKeywords[] = $clean;
            }
        }
        
        return implode(', ', $cleanKeywords);
    }

    /**
     * Sanitiza arrays de strings
     */
    private function sanitizeArray(?array $items): ?array
    {
        if (!$items) return $items;
        
        $sanitized = [];
        foreach ($items as $key => $value) {
            // Sanitizar tanto la clave como el valor
            $cleanKey = is_string($key) ? $this->sanitizeText($key) : $key;
            $cleanValue = is_string($value) ? $this->sanitizeText($value) : $value;
            
            if (!empty($cleanValue)) {
                $sanitized[$cleanKey] = $cleanValue;
            }
        }
        
        return $sanitized;
    }
}