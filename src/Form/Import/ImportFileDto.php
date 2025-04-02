<?php

namespace App\Form\Import;

class ImportFileDto
{
    private $file;

    public function file()
    {
        return $this->file;
    }

    public function setFile($file): void
    {
        $this->file = $file;
    }
}