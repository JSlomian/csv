<?php

declare(strict_types=1);

namespace Jslomian\Csv\View;

use RuntimeException;

final readonly class ViewRenderer
{
    public function __construct(private string $templateDirectory)
    {
    }

    /**
     * @param array<string, mixed> $data
     */
    public function render(string $template, array $data = []): string
    {
        $templatePath = sprintf('%s/%s.php', rtrim($this->templateDirectory, '/\\'), $template);

        if (!is_file($templatePath)) {
            throw new RuntimeException(sprintf('Template not found: %s', $templatePath));
        }

        extract($data, EXTR_SKIP);

        ob_start();
        require $templatePath;

        return (string) ob_get_clean();
    }
}
