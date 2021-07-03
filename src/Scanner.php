<?php declare(strict_types=1);

namespace Kur4tor\TwigScanner;

use Kur4tor\TwigScanner\Node\MacroNode;

class Scanner
{
    private Parser $parser;

    private array $macros;

    private array $aliases;

    public function __construct()
    {
        $this->parser = new Parser();
        $this->macros = [];
        $this->aliases = [];
    }

    public function scan($directory) {
        $Directory = new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS);
        $Iterator = new \RecursiveIteratorIterator($Directory);
        $Regex = new \RegexIterator($Iterator,'/^.+\.twig/i',\RecursiveRegexIterator::GET_MATCH);

        foreach ($Regex as $fileName) {
            $this->parser->parseFile($fileName[0]);
        }

        $parsed = $this->parser->readParsedData();

        // process all macros
        foreach ($parsed as $fileName => $items) {
            if (isset($items['macros']) && count($items['macros'], COUNT_RECURSIVE)) {
                $this->processMacros($fileName, $items['macros']);
            }
        }

        // process usage
        foreach ($parsed as $fileName => $items) {
            $this->aliases = [];
            if (isset($items['aliases']) && count($items['aliases'], COUNT_RECURSIVE)) {
                $this->processAliases($items['aliases']);
            }

            if (isset($items['usageMacros']) && count($items['usageMacros'], COUNT_RECURSIVE)) {
                $this->processUsage($fileName, $items['usageMacros']);
            }
        }

        var_dump($this->macros);
        // TODO export report file
    }

    private function getMacro(string $fileName, string $name): MacroNode {
        foreach($this->macros as $macro) {
            /* @var MacroNode $macro */

            if($macro->getName() === $name && stripos($macro->getFile(), $fileName)) {
                return $macro;
            }
        }

        $macro = new MacroNode($name, $fileName);
        $this->macros[] = $macro;

        return $macro;
    }

    private function processUsage(string $fileName, array $usags): void {
        $aliases = $usags[1];
        $macroNames = $usags[2];
        $attributes = $usags[3];

        foreach ($macroNames as $key => $macroName) {
            $macroFile = $this->getFileNameByAlias($aliases[$key]);

            if($macroFile) {
                $macro = $this->getMacro($macroFile, $macroName);
                $macro->addImportingFile($fileName);
                $macro->markAsCalled();
                if(isset($attributes[$key])) {
                    $attributesValuesArray = array_map('trim', explode(",", $attributes[$key]));
                    if ($attributesValuesArray !== []) {
                        foreach ($attributesValuesArray as $index => $value) {
                           $attr = $macro->findAttributeByIndex($index);
                           if ($attr) {
                               $attr->markAsCalled();
                           }
                        }
                    }
                }
            }
        }
    }

    private function getFileNameByAlias(string $alias): ?string {
        foreach($this->aliases as $fileName => $aliases) {
            if(in_array($alias, $aliases)) {
                return $fileName;
            }
        }

        return null;
    }

    private function processAliases($aliases): void {
        $fileNames = $aliases[1];
        $aliasesNames = $aliases[2];

        foreach($fileNames as $key => $macroFileName) {
            if (!isset($this->aliases[$macroFileName])) {
                $this->aliases[$macroFileName] = [];
            }
            array_push($this->aliases[$macroFileName], $aliasesNames[$key]);
        }
    }

    private function processMacros($fileName, $macros): void {
        $names = $macros[1];
        $attributes = $macros[2];

        foreach($names as $key => $name) {
            if(isset($attributes[$key])) {
                $attributesArray = array_map('trim', explode(",", $attributes[$key]));
                $macro = $this->getMacro($fileName, $name);
                if ($attributesArray !== []) {
                    foreach ($attributesArray as $attribute) {
                        $macro->addAttribute($attribute);
                    }
                }
            }
        }
    }
}