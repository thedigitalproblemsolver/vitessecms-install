<?php declare(strict_types=1);

namespace VitesseCms\Install\Models;

use ArrayIterator;

class MigrationIterator extends ArrayIterator
{
    public function __construct(array $products)
    {
        parent::__construct($products);
    }

    public function current(): Migration
    {
        return parent::current();
    }

    public function add(Migration $value): void
    {
        $this->append($value);
    }
}