<?php

declare(strict_types = 1);

namespace Sweetchuck\JunitMerger;

class JunitMergerSubstr extends JunitMergerBase
{

    protected int $headerLength = 0;

    public function getHeaderLength(): int
    {
        return $this->headerLength;
    }

    /**
     * @return $this
     */
    public function setHeaderLength(int $headerLength)
    {
        $this->headerLength = $headerLength;

        return $this;
    }

    public function detectHeaderLength(string $xmlString): int
    {
        $rootNodeName = $this->getRootNodeName();
        // @todo Open tag attributes.
        $openTagVariations = [
            "<{$rootNodeName}>",
            "<{$rootNodeName}/>",
            "<{$rootNodeName} />",
        ];
        foreach ($openTagVariations as $openTag) {
            $position = strpos($xmlString, $openTag);
            if ($position === false) {
                continue;
            }

            $this->setHeaderLength($position + strlen($openTag) + 1);
        }

        return $this->getHeaderLength();
    }

    protected int $footerLength = 0;

    public function getFooterLength(): int
    {
        return $this->footerLength;
    }

    /**
     * @return $this
     */
    public function setFooterLength(int $footerLength)
    {
        $this->footerLength = $footerLength;

        return $this;
    }

    public function detectFooterLength(string $xmlString): int
    {
        $rootNodeName = $this->getRootNodeName();
        $closeTagVariations = [
            "</{$rootNodeName}>",
            "<{$rootNodeName}/>",
            "<{$rootNodeName} />",
        ];
        foreach ($closeTagVariations as $closeTag) {
            $position = strrpos($xmlString, $closeTag);
            if ($position === false) {
                continue;
            }
            $this->setFooterLength(strlen($xmlString) - $position);
        }

        return $this->getFooterLength();
    }

    public function addXmlString(string $xmlString)
    {
        $this->output->write(substr(
            $xmlString,
            $this->getHeaderLength() ?: $this->detectHeaderLength($xmlString),
            0 - ($this->getFooterLength() ?: $this->detectFooterLength($xmlString)),
        ));

        return $this;
    }
}
