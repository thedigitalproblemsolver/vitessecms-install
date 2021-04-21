<?php declare(strict_types=1);

namespace VitesseCms\Install\Models;

use VitesseCms\Database\AbstractCollection;

class Migration extends AbstractCollection {
    /**
     * @var string
     */
    public $name;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Migration
    {
        $this->name = $name;

        return $this;
    }
}