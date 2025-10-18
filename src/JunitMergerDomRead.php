<?php

declare(strict_types = 1);

namespace Sweetchuck\JunitMerger;

/**
 * This class uses \DOMDocument to parse the input XML, but generates the merged
 * XML with string concatenation.
 */
class JunitMergerDomRead extends JunitMergerBase
{

    public function addXmlString(string $xmlString): static
    {
        $xml = new \DOMDocument();
        $xml->formatOutput = true;
        $xml->preserveWhiteSpace = true;
        // @todo Error handling.
        $xml->loadXML($xmlString);
        $xpath = new \DOMXPath($xml);
        $elements = $xpath->query('/' . $this->getRootNodeName());
        if (!$elements || $elements->count() === 0) {
            return $this;
        }

        /** @var \DOMElement $root */
        $root = $elements->item(0);
        foreach ($root->childNodes as $childNode) {
            $this->output->writeln($xml->saveXML($childNode) ?: '');
        }

        return $this;
    }
}
