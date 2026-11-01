<?php

namespace App\Helpers;

class View
{
    public static function render(string $template, array $data = [], ?string $layout = 'layouts/main'): void
    {
        // Normalize template path before extracting any variables
        $templateFile = base_path('templates/' . str_replace('.', '/', $template) . '.php');

        if (!file_exists($templateFile)) {
            throw new \RuntimeException("View template not found: {$templateFile}");
        }

        // Render template in an isolated scope
        $content = self::renderFile($templateFile, $data);

        if ($layout !== null) {
            $layoutFile = base_path('templates/' . str_replace('.', '/', $layout) . '.php');
            if (!file_exists($layoutFile)) {
                throw new \RuntimeException("Layout not found: {$layoutFile}");
            }
            $layoutData = array_merge($data, ['content' => $content]);
            echo self::renderFile($layoutFile, $layoutData);
        } else {
            echo $content;
        }
        exit;
    }

    private static function renderFile(string $filePath, array $data): string
    {
        ob_start();
        extract($data);
        require func_get_arg(0);
        return ob_get_clean();
    }
}

