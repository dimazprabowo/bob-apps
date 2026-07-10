<?php

namespace App\Services\Traits;

trait SanitizesInput
{
    protected function sanitize(?string $value): ?string
    {
        if ($value === null) return null;
        return trim(strip_tags($value));
    }

    protected function sanitizeArray(array $keys, array $data): array
    {
        foreach ($keys as $key) {
            if (isset($data[$key]) && is_string($data[$key])) {
                $data[$key] = $this->sanitize($data[$key]);
            }
        }

        return $data;
    }
}
