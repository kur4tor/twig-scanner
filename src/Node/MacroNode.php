<?php declare(strict_types=1);

namespace Kur4tor\TwigScanner\Node;

class MacroNode
{
    private string $ident;
    private string $name;
    private array $attributes;
    private int $instances;
    private string $file;
    private array $importingInFiles;

    public function __construct(string $name, string $file)
    {
        $this->attributes = [];
        $this->importingInFiles = [];
        $this->name = $name;
        $this->file = $file;
        $this->instances = 0;
        $this->ident = sprintf("%s_%s", md5($file), $name);
    }

    public function addAttribute(string $name)
    {
        $this->attributes[] = new MacroAttributeNode($name);
    }

    public function markAsCalled(): void
    {
        $this->instances +=1;
    }

    public function findAttribute(string $name): ?MacroAttributeNode
    {
        foreach($this->attributes as $attribute) {
            /* @var MacroAttributeNode $attribute */
            if( $attribute->getName() === $name) {
                return $attribute;
            }
        }

        return null;
    }

    public function findAttributeByIndex(int $index)
    {
        foreach($this->attributes as $key => $attribute) {
            /* @var MacroAttributeNode $attribute */
            if( $index === $key) {
                return $attribute;
            }
        }

        return null;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIdent(): string
    {
        return $this->ident;
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function getImportingInFiles(): array
    {
        return $this->importingInFiles;
    }

    public function addImportingFile(string $file): void
    {
        $this->importingInFiles[$file] = $file;
    }

    public function getInstances(): int
    {
        return $this->instances;
    }
}