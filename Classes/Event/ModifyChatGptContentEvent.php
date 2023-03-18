<?php
declare(strict_types=1);

namespace Ayacoo\NewsTldr\Event;

final class ModifyChatGptContentEvent
{
    public function __construct(
        protected array  $row = [],
        protected string $content = ''
    )
    {
    }

    /**
     * @return array
     */
    public function getRow(): array
    {
        return $this->row;
    }

    /**
     * @param array $row
     */
    public function setRow(array $row): void
    {
        $this->row = $row;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }
}
