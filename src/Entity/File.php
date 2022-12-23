<?php

namespace App\Entity;

use App\Repository\FileRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FileRepository::class)
 */
class File
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $file_name;

    /**
     * @ORM\Column(type="integer")
     */
    private $record_count;

    /**
     * @ORM\Column(type="smallint")
     */
    private $status;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $uploaded_at;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $mapping;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $new_name;

    // private $new_name;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFileName(): ?string
    {
        return $this->file_name;
    }

    public function setFileName(string $file_name): self
    {
        $this->file_name = $file_name;

        return $this;
    }

    public function getRecordCount(): ?int
    {
        return $this->record_count;
    }

    public function setRecordCount(int $record_count): self
    {
        $this->record_count = $record_count;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getUploadedAt(): ?\DateTimeInterface
    {
        return $this->uploaded_at;
    }

    public function setUploadedAt(?\DateTimeInterface $uploaded_at): self
    {
        $this->uploaded_at = $uploaded_at;

        return $this;
    }
    public function getMapping(): ?string
    {
        return $this->mapping;
    }

    public function setMapping(string $mapping): self
    {
        $this->mapping = $mapping;

        return $this;
    }

    public function getNewName(): ?string
    {
        return $this->new_name;
    }

    public function setNewName(?string $new_name): self
    {
        $this->new_name = $new_name;

        return $this;
    }
}
