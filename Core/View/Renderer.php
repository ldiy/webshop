<?php

namespace Core\View;

use Throwable;

class Renderer
{
    protected string $viewPath;

    public function __construct(string $viewPath)
    {
        $this->viewPath = $viewPath;
    }

    /**
     * Render a view
     *
     * @param string $view
     * @param array $params
     * @return string
     * @throws Throwable
     */
    public function render(string $view, array $params = []): string
    {
        $viewFile = $this->getViewPath($view);
        if (!file_exists($viewFile)) {
            throw new \InvalidArgumentException('View file "' . $viewFile . '" not found');
        }

        $errors = session()->get('errors') ?? [];

        $params = array_merge($params, [
            'errors' => $errors,
        ]);

        try {
            ob_start();
            $this->protectedIncludeScope($viewFile, $params);
            $content = ob_get_clean();
        } catch (Throwable $e) {
            ob_end_clean();
            throw $e;
        }

        return $content;
    }

    /**
     * Load the view file in a limited scope
     *
     * @param string $viewFile
     * @param array $params
     * @return void
     */
    private function protectedIncludeScope(string $viewFile, array $params = []): void
    {
        extract($params);
        include $viewFile;
    }

    /**
     * Get the full path to a view file
     *
     * @param $view
     * @return string
     */
    private function getViewPath($view): string
    {
        return $this->viewPath . DIRECTORY_SEPARATOR . $view . '.php';
    }

    /**
     * Check if a view exists
     *
     * @param string $view
     * @return bool
     */
    public function viewExists(string $view): bool
    {
        return file_exists($this->getViewPath($view));
    }
}