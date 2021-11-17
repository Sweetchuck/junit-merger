<?php

declare(strict_types = 1);

namespace Sweetchuck\JunitMerger;

use Symfony\Component\Console\Output\OutputInterface;

abstract class JunitMergerBase implements JunitMergerInterface
{

    protected OutputInterface $output;

    protected string $rootNodeName = 'testsuites';

    public function getRootNodeName(): string
    {
        return $this->rootNodeName;
    }

    /**
     * {@inheritdoc}
     */
    public function setRootNodeName(string $rootNodeName)
    {
        $this->rootNodeName = $rootNodeName;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function mergeXmlFiles(iterable $xmlFiles, OutputInterface $output)
    {
        return $this
            ->start($output)
            ->addXmlFiles($xmlFiles)
            ->finish();
    }

    /**
     * {@inheritdoc}
     */
    public function mergeXmlStrings(\Iterator $xmlStrings, OutputInterface $output)
    {
        return $this
            ->start($output)
            ->addXmlStrings($xmlStrings)
            ->finish();
    }

    /**
     * {@inheritdoc}
     */
    public function start(OutputInterface $output)
    {
        $this->output = $output;

        $this->output->writeln('<?xml version="1.0" encoding="UTF-8"?>');
        $this->output->writeln('<' . $this->getRootNodeName() . '>');

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addXmlFiles(\Iterator $xmlFiles)
    {
        while ($xmlFiles->valid()) {
            $this->addXmlFile($xmlFiles->current());
            $xmlFiles->next();
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addXmlFile($xmlFile)
    {
        $filename = $xmlFile instanceof \SplFileInfo ? $xmlFile->getPathname() : rtrim($xmlFile, "\r\n");
        $filename = preg_replace(
            '@^/proc/self/fd/(?P<id>\d+)$@',
            'php://fd/$1',
            $filename,
        );

        if ($filename === '') {
            return $this;
        }

        $this->addXmlString(file_get_contents($filename));

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addXmlStrings(\Iterator $xmlStrings)
    {
        while ($xmlStrings->valid()) {
            $this->addXmlString($xmlStrings->current());
            $xmlStrings->next();
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    abstract public function addXmlString(string $xmlString);

    /**
     * {@inheritdoc}
     */
    public function finish()
    {
        $this->output->writeln('</' . $this->getRootNodeName() . '>');

        return $this;
    }
}
