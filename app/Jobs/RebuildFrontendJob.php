<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RebuildFrontendJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        $this->onQueue('deployments'); // job unico para evitar colisiones con otros jobs
    }

    public function handle(): void
    {
        

        try {
            $token = $this->getGitHubAppToken();
            $repo = 'YUNTAS-PUBLICIDAD/Yuntas-Frontend-2025';

            Http::withHeaders([
                'Accept' => 'application/vnd.github+json',
                'Authorization' => "Bearer {$token}",
                'X-GitHub-Api-Version' => '2022-11-28',
            ])->post("https://api.github.com/repos/{$repo}/dispatches", [
                'event_type' => 'rebuild-frontend',
                'client_payload' => [
                    'triggered_by' => 'scheduled_job',
                    'timestamp' => now()->toIso8601String(),
                ],
            ]);

            Log::info('Frontend rebuild triggered successfully');
        } catch (\Exception $e) {
            Log::error('Failed to trigger frontend rebuild: ' . $e->getMessage());
        } finally {
            optional($lock)->release();
        }
    }

     /**
     * Trigger GitHub Action for Frontend Rebuild
     */
    private function triggerFrontendRebuild(): void
    {
        // Lock para evitar builds simultáneos
        $lock = Cache::lock('frontend-rebuild', 180); // 1 job cada 3 minutos como máximo

        if (!$lock->get()) {
            Log::info('Frontend rebuild skipped: another build in progress');
            return;
        }

        try {
            $token = $this->getGitHubAppToken();
            $repo = env('GITHUB_REPO');

            $response = Http::withHeaders([
                'Accept' => 'application/vnd.github+json',
                'Authorization' => "Bearer {$token}",
                'X-GitHub-Api-Version' => '2022-11-28',
            ])->post("https://api.github.com/repos/{$repo}/dispatches", [
                'event_type' => 'rebuild-frontend',
                'client_payload' => [
                    'triggered_by' => 'product_update',
                    'timestamp' => now()->toIso8601String(),
                ],
            ]);

            if ($response->successful()) {
                Log::info('Rebuild del frontend activado correctamente');
            } else {
                Log::warning('Respuesta del webhook de GitHub: ' . $response->status() . ' - ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('Error al activar el rebuild del frontend: ' . $e->getMessage());
        } finally {
            optional($lock)->release();
        }
    }

    private function getGitHubAppToken(): string
    {
        $appId = env('GITHUB_APP_ID');
        $installationId = env('GITHUB_APP_INSTALLATION_ID');

        if (!$appId || !$installationId) {
            throw new \Exception('Credenciales GITHUB_APP_ID o GITHUB_APP_INSTALLATION_ID no configuradas');
        }
        
        $privateKey = null;

        // Intentar cargar desde archivo
        $privateKeyPath = env('GITHUB_APP_PRIVATE_KEY_PATH');
        
        if ($privateKeyPath) {
            if (strpos($privateKeyPath, '/') !== 0) {
                $privateKeyPath = base_path($privateKeyPath);
            }
            
            if (file_exists($privateKeyPath)) {
                $privateKey = file_get_contents($privateKeyPath);
            }
        }
        
        if (!$privateKey) { // Intentar cargar desde variable de entorno
            $privateKey = env('GITHUB_APP_PRIVATE_KEY');
        }
        
        if (!$privateKey) {
            Log::error('Clave privada de GitHub App no encontrada', [
                'path' => $privateKeyPath,
                'exists' => file_exists($privateKeyPath ?? ''),
                'base_path' => base_path()
            ]);
            throw new \Exception('Clave privada de GitHub App no encontrada');
        }

        $jwt = $this->generateJWT($privateKey, $appId);

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$jwt}",
            'Accept' => 'application/vnd.github+json',
            'X-GitHub-Api-Version' => '2022-11-28',
        ])->post("https://api.github.com/app/installations/{$installationId}/access_tokens");

        if (!$response->successful()) {
            throw new \Exception('No se pudo obtener el token de la GitHub App: ' . $response->body());
        }

        return $response->json()['token'];
    }

    // NOTA: no usamos firebase/php-jwt porque da muchos errores al instalarlo en nuestro caso, asi que lo hacemos manualmente
    private function generateJWT(string $privateKey, int $appId): string
    {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'RS256']);
        $payload = json_encode([
            'iat' => time(),
            'exp' => time() + 600,
            'iss' => $appId,
        ]);

        $base64UrlHeader = $this->base64UrlEncode($header);
        $base64UrlPayload = $this->base64UrlEncode($payload);

        $signature = '';
        $success = openssl_sign(
            $base64UrlHeader . "." . $base64UrlPayload,
            $signature,
            $privateKey,
            OPENSSL_ALGO_SHA256
        );

        if (!$success) {
            throw new \Exception('Error al firmar el token JWT');
        }

        $base64UrlSignature = $this->base64UrlEncode($signature);

        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    private function base64UrlEncode(string $data): string
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
    }
}