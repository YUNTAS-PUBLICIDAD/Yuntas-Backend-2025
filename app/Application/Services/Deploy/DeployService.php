<?php

namespace App\Application\Services\Deploy;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class DeployService
{
    private const DEPLOY_IN_PROGRESS_KEY = 'deploy_en_proceso';
    private const DEPLOY_LOCK_TTL = 360; // 6 minutos máximo si algo sale mal

    public function triggerDeploy(): array
    {

        // Verificar si hay un deploy en progreso
        if ($this->isDeployInProgress()) {
            $startedAt = \Carbon\Carbon::parse(Cache::get(self::DEPLOY_IN_PROGRESS_KEY));
            $elapsed = $startedAt->diffInSeconds(now());
            $elapsedMinutes = (int) ($elapsed / 60);
            $elapsedSeconds = $elapsed % 60;
            
            Log::info('Intento de deploy bloqueado - Deploy en progreso', [
                'started_at' => $startedAt->toIso8601String(),
                'now' => now()->toIso8601String(),
                'elapsed_seconds' => $elapsed
            ]);

            $timeText = $elapsedMinutes > 0 
                ? "{$elapsedMinutes}m {$elapsedSeconds}s"
                : "{$elapsedSeconds}s";
            
            return [
                'success' => false,
                'message' => "Ya hay un deploy en progreso. Por favor espera a que termine. Tiempo transcurrido: {$timeText}."
            ];
        }
        try {
            // Marcar inicio del deploy
            $this->markDeployInProgress();

            $token = $this->getGitHubAppToken();
            $repo = env('GITHUB_REPO');

            $httpRequest = Http::withHeaders([
                'Accept' => 'application/vnd.github+json',
                'Authorization' => "Bearer {$token}",
                'X-GitHub-Api-Version' => '2022-11-28',
            ]);

            if (app()->environment('local')) {
                $httpRequest = $httpRequest->withoutVerifying();
            }

            $branch = env('GITHUB_BRANCH', 'master');
            $response = $httpRequest->post("https://api.github.com/repos/{$repo}/actions/workflows/deploy.yml/dispatches", [
                'ref' => $branch,
            ]);

            if ($response->successful()) {
                Log::info('Deploy del frontend activado correctamente');
                return [
                    'success' => true,
                    'message' => 'Deploy iniciado correctamente. El proceso puede tardar minutos.',
                ];
            }

            // Si falla la petición, liberar el lock
            $this->releaseDeployLock();

            Log::warning('Respuesta del webhook de GitHub', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [
                'success' => false,
                'message' => 'Error al iniciar deploy',
            ];

        } catch (\Exception $e) {
            $this->releaseDeployLock();
            Log::error('Error al disparar deploy', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Error al iniciar deploy: ' . $e->getMessage()
            ];
        }
    }

    public function isDeployInProgress(): bool
    {
        return Cache::has(self::DEPLOY_IN_PROGRESS_KEY);
    }

    public function markDeployInProgress(): void
    {
        Cache::put(self::DEPLOY_IN_PROGRESS_KEY, now()->toIso8601String(), self::DEPLOY_LOCK_TTL);
    }

    public function releaseDeployLock(): void
    {
        Cache::forget(self::DEPLOY_IN_PROGRESS_KEY);
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

        $tokenRequest = Http::withHeaders([
            'Authorization' => "Bearer {$jwt}",
            'Accept' => 'application/vnd.github+json',
            'X-GitHub-Api-Version' => '2022-11-28',
        ]);

        if (app()->environment('local')) {
            $tokenRequest = $tokenRequest->withoutVerifying();
        }

        $response = $tokenRequest->post("https://api.github.com/app/installations/{$installationId}/access_tokens");

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