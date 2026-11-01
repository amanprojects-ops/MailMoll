<?php

namespace App\Helpers;

class View
{
    public static function render(string $template, array $data = [], ?string $layout = 'layouts/main'): void
    {
        // Extract variables to local scope
        extract($data);

        // Normalize template path
        $templateFile = base_path('templates/' . str_replace('.', '/', $template) . '.php');

        if (!file_exists($templateFile)) {
            throw new \RuntimeException("View template not found: {$templateFile}");
        }

        // Render template into content buffer
        ob_start();
        require $templateFile;
        $content = ob_get_clean();

        if ($layout !== null) {
            $layoutFile = base_path('templates/' . str_replace('.', '/', $layout) . '.php');
            if (!file_exists($layoutFile)) {
                throw new \RuntimeException("Layout not found: {$layoutFile}");
            }
            require $layoutFile;
        } else {
            echo $content;
        }
        exit;
    }
}
