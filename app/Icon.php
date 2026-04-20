<?php

namespace App;

/**
 * Description of Icon
 *
 * @author tibo
 */
class Icon
{

    private string $name;
    
    const DEFAULT = "server.png";

    public function __construct(?string $name)
    {
        if (is_null($name)) {
            $this->name = self::DEFAULT;
            return;
        }

        if (str_contains($name, '..') || str_contains($name, '/') || str_contains($name, '\\')
                || str_contains($name, "\0")) {
            throw new \InvalidArgumentException('Invalid icon name.');
        }

        $this->name = $name;
    }

    public function url() : string
    {
        return '/images/icons/' . $this->name;
    }
    
    public function name() : string
    {
        return $this->name;
    }

    /**
     * List all available icons
     * @return Icon[]
     */
    public static function all() : array
    {
        $icons = [];
        $directory = __DIR__ . '/../public/images/icons';

        if (is_dir($directory)) {
            $files = glob($directory . '/*.png');
            foreach ($files as $file) {
                $name = basename($file);
                $icons[] = new self($name);
            }
        }

        return $icons;
    }
}
