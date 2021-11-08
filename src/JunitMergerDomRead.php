<?php

declare(strict_types = 1);

namespace Sweetchuck\JunitMerger;

/**
 * This class uses \DOMDocument to parse the input XML, but generates the merged
 * XML with string concatenation.
 */
class JunitMergerDomRead extends JunitMergerBase
{

    /**
     * {@inheritdoc}
     */
    public function addXmlString(string $xmlString)
    {
        $xml = new \DOMDocument();
        $xml->formatOutput = true;
        $xml->preserveWhiteSpace = true;
        // @todo Error handling.
        $xml->loadXML($xmlString);
        $xpath = new \DOMXPath($xml);
        /** @var \DOMElement $root */
        $root = $xpath->query('/' . $this->getRootNodeName())->item(0);
        foreach ($root->childNodes as $childNode) {
            $this->output->writeln($xml->saveXML($childNode));
        }

        return $this;
    }
}
