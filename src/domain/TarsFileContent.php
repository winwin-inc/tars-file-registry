<?php


namespace winwin\tars\file\domain;

use kuiper\db\annotation\Id;

class TarsFileContent
{
    /**
     * @Id()
     * @var string
     */
    private $filePath;

    /**
     * @var string
     */
    private $content;

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * @param string $filePath
     *
     * @return static
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;

        return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     *
     * @return static
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

}