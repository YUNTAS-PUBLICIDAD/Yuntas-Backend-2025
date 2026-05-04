<?php
namespace App\Application\Services\Template;

use App\Models\AutomationRule;
use App\Models\ProductTemplateAsset;

class TemplateResolverService
{
    public function resolve(string $event, array $context = [])
    {
        $rule = $this->findRule($event);

        if (!$rule) {
            return null;
        }

        $action = $this->selectAction($rule);

        if (!$action || !$action->variant) {
            return null;
        }

        $variant = $action->variant;

        return [
            'variant_id' => $variant->id,
            'channel' => $variant->channel,
            'context' => $variant->context,
            'subject' => $variant->subject,
            'content' => $this->renderContent($variant->content, $context),
            'assets' => $this->resolveAssets($variant, $context),
            'delay_seconds' => $action->delay_seconds,
        ];
    }

    private function findRule(string $event)
    {
        return AutomationRule::query()
            ->where('event', $event)
            ->where('active', true)
            ->first();
    }

    private function selectAction($rule)
    {
        return $rule->actions()
            ->with(['variant.assets'])
            ->orderByDesc('priority')
            ->orderBy('delay_seconds')
            ->first();
    }

    private function renderContent(string $content, array $context): string
    {
        foreach ($context as $key => $value) {
            if (is_array($value) || is_object($value)) {
                continue;
            }

            $content = str_replace(
                ['{{'.$key.'}}', '{{ '.$key.' }}'],
                (string) $value,
                $content
            );
        }

        return $content;
    }

    private function resolveAssets($variant, array $context): array
    {
        $assets = [];

        foreach ($variant->assets as $asset) {
            $assets[$asset->key] = [
                'path' => $asset->path,
                'meta' => $asset->meta ?? null,
            ];
        }

        // override por producto (si aplica)
        if (!empty($context['product_id'])) {
            $assets = $this->applyProductOverrides(
                $assets,
                $variant->id,
                (int) $context['product_id']
            );
        }

        return $assets;
    }

    private function applyProductOverrides(array $assets, int $variantId, int $productId): array
    {
        $overrides = ProductTemplateAsset::query()
            ->where('product_id', $productId)
            ->where('template_variant_id', $variantId)
            ->get();

        foreach ($overrides as $override) {
            $assets[$override->key] = [
                'path' => $override->path,
                'meta' => null,
            ];
        }

        return $assets;
    }
}
