<?php declare(strict_types=1);

namespace Kur4tor\TwigScanner;

class Parser
{
    private array $files;

    public function __construct()
    {
        $this->files = [];
    }

    public function parseFile($file) {

        $this->files[$file] = [
            'aliases' => [],
            'macros' => [],
            'usageMacros' => [],
        ];
        if(!is_file($file)) return false;

        $data = strip_tags(file_get_contents($file));

        preg_match_all(	'{%\s*import\s*"(.*)"\s*as\s*(\w*)\s*%}',
            $data, $this->files[$file]['aliases']);

        preg_match_all(	'{%\s*macro\s*(.*)\(\s*([^)]+?)\s*\)\s*%}',
            $data, $this->files[$file]['macros']);

        preg_match_all(	'/{{\s*(.*)\.(.*)\(\s*([^)]+?)\s*\)\s*}}/',
            $data, $this->files[$file]['usageMacros']);

        return $this->files[$file];
    }

    public function readParsedData(): array
    {
        return $this->files;
    }
}