<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait CreatedUpdatedTrait
{
    /**
     * @var \DateTime
     */
    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: true)]
    protected $createdAt;

    /**
     * @var \DateTime
     */
    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: true)]
    protected $updatedAt;

    public function updatedDatetime(): void
    {
        $this->updatedAt = new \DateTime();
        if (null === $this->createdAt) {
            $this->createdAt = new \DateTime();
        }
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }
}
