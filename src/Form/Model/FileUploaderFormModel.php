<?php


namespace App\Form\Model;


use Doctrine\ORM\Mapping as ORM;

class FileUploaderFormModel
{
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imageFilename;

    /**
     * @return mixed
     */
    public function getImageFilename()
    {
        return $this->imageFilename;
    }

    /**
     * @param mixed $imageFilename
     */
    public function setImageFilename($imageFilename): void
    {
        $this->imageFilename = $imageFilename;
    }
}