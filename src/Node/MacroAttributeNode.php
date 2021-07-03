<?php declare(strict_types=1);

namespace Kur4tor\TwigScanner\Node;


class MacroAttributeNode
{
    private string $name;
    private int $counter = 0;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function calls(): int
    {
        return $this->counter;
    }

    public function markAsCalled(): void
    {
        $this->counter += 1;
    }

    public function getName(): string
    {
        return $this->name;
    }
}