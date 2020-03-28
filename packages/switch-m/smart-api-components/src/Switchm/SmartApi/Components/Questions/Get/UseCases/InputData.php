<?php

namespace Switchm\SmartApi\Components\Questions\Get\UseCases;

class InputData
{
    private $keyWord;

    private $qGroup;

    private $tag;

    /**
     * SampleCountGetQuestionInputData constructor.
     * @param null|string $keyWord
     * @param string $qGroup
     * @param string $tag
     */
    public function __construct(?string $keyWord, string $qGroup, string $tag)
    {
        $this->keyWord = $keyWord;
        $this->qGroup = $qGroup;
        $this->tag = $tag;
    }

    /**
     * @return null|string
     */
    public function keyWord(): ?string
    {
        return $this->keyWord;
    }

    /**
     * @return string
     */
    public function qGroup(): string
    {
        return $this->qGroup;
    }

    /**
     * @return string
     */
    public function tag(): string
    {
        return $this->tag;
    }
}
