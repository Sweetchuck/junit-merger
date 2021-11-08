<?php

declare(strict_types = 1);

namespace Sweetchuck\JunitMerger;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * PHPUnit uses a <warning /> tag, which is not mentioned in the .xsd.
 *
 * @link https://github.com/junit-team/junit5/blob/main/platform-tests/src/test/resources/jenkins-junit.xsd
 */
class JunitMergerDomReadWrite extends JunitMergerBase
{

    protected \DOMDocument $merged;

    protected \DOMXPath $mergedXpath;

    public function start(OutputInterface $output)
    {
        $this->output = $output;
        $this->initMerged();

        return $this;
    }

    public function finish()
    {
        /** @var \DOMElement $dstRoot */
        $dstRoot = $this->mergedXpath->query('/testsuites')->item(0);
        $this->updateStats($dstRoot);

        $this->output->write($this->merged->saveXML());

        return $this;
    }

    protected function initMerged()
    {
        $this->merged = new \DOMDocument('1.0', 'UTF-8');
        $this->merged->preserveWhiteSpace = true;
        $this->merged->formatOutput = true;

        $root = $this->merged->createElement($this->getRootNodeName());
        $this->merged->appendChild($root);

        $this->mergedXpath = new \DOMXPath($this->merged);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addXmlString(string $xmlString)
    {
        $src = new \DOMDocument();
        $src->formatOutput = true;
        $src->preserveWhiteSpace = true;
        $src->loadXML($xmlString);
        $srcXpath = new \DOMXPath($src);
        /** @var \DOMElement $srcRoot */
        $srcRoot = $srcXpath->query('/' . $this->getRootNodeName())->item(0);

        /** @var \DOMElement $dstRoot */
        $dstRoot = $this->mergedXpath->query('/testsuites')->item(0);

        /** @var \DOMNode $srcChild */
        foreach ($srcRoot->childNodes as $srcChild) {
            if ($srcChild->nodeType !== \XML_ELEMENT_NODE) {
                continue;
            }

            /** @var \DOMElement $srcChild */
            $suiteName = $srcChild->getAttribute('name');

            $dstChild = $this->findChildByName($dstRoot, $suiteName);
            if (!$dstChild) {
                $dstChild = $this->merged->importNode($srcChild, true);
                $dstRoot->appendChild($dstChild);

                continue;
            }

            $this->mergeSuites($srcChild, $dstChild);
        }

        return $this;
    }

    protected function findChildByName(\DOMElement $parent, string $name): ?\DOMElement
    {
        $list = $this->mergedXpath->query(
            sprintf(
                './testsuite[@name="%s"]|./testcase[@name="%s"]',
                // @todo Escape.
                $name,
                $name,
            ),
            $parent,
        );

        return $list->count() > 0 ? $list->item(0) : null;
    }

    protected function mergeSuites(\DOMElement $src, \DOMElement $dst)
    {
        $xpath = new \DOMXPath($src->ownerDocument);
        /** @var \DOMElement $child */
        foreach ($xpath->query('./testsuite|./testcase', $src) as $child) {
            $list = $this->mergedXpath->query(
                sprintf(
                    './%s[@name="%s"]',
                    $child->tagName,
                    $child->getAttribute('name'),
                ),
                $dst,
            );

            if ($list->count() === 0) {
                $clone = $dst->ownerDocument->importNode($child, true);
                $dst->appendChild($clone);

                continue;
            }

            /** @var \DOMElement $dstChild */
            $dstChild = $list->item(0);

            if ($child->tagName === 'testsuite') {
                $this->mergeSuites($child, $dstChild);
            }

            if ($child->tagName === 'testcase') {
                $dstChild->remove();
                $clone = $dst->ownerDocument->importNode($child, true);
                $dst->appendChild($clone);
            }
        }

        return $this;
    }

    protected function updateStats(\DOMElement $suite)
    {
        $stats = [
            'tests' => 0,
            'assertions' => 0,
            'errors' => 0,
            'warnings' => 0,
            'failures' => 0,
            'skipped' => 0,
            'time' => 0,
        ];

        $resultAttributes = [
            'errors' => './error',
            'warnings' => './warning',
            'failures' => './failure',
            'skipped' => './skipped',
        ];

        $children = $this->mergedXpath->query('./testsuite', $suite);
        /** @var \DOMElement $child */
        foreach ($children as $child) {
            $this->updateStats($child);

            foreach (array_keys($stats) as $attrName) {
                if (!$child->hasAttribute($attrName)) {
                    continue;
                }

                $value = $child->getAttribute($attrName);
                settype($value, $attrName === 'time' ? 'float' : 'integer');
                $stats[$attrName] += $value;
            }
        }

        $children = $this->mergedXpath->query('./testcase', $suite);
        /** @var \DOMElement $child */
        foreach ($children as $child) {
            $stats['tests']++;

            if ($child->hasAttribute('assertions')) {
                $stats['assertions'] += (int) $child->getAttribute('assertions');
            }

            if ($child->hasAttribute('time')) {
                $stats['time'] += (float) $child->getAttribute('time');
            }

            foreach ($resultAttributes as $attrName => $xpathQuery) {
                $list = $this->mergedXpath->query($xpathQuery, $child);
                $stats[$attrName] += $list->count();
            }
        }

        foreach ($stats as $attrName => $attrValue) {
            $suite->setAttribute($attrName, (string) $attrValue);
        }

        return $this;
    }
}
