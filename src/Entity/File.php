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
    private $uploaded_time;

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

    public function getUploadedTime(): ?\DateTimeInterface
    {
        return $this->uploaded_time;
    }

    public function setUploadedTime(?\DateTimeInterface $uploaded_time): self
    {
        $this->uploaded_time = $uploaded_time;

        return $this;
    }
}
