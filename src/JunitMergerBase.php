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

    public function setRootNodeName(string $rootNodeName): static
    {
        $this->rootNodeName = $rootNodeName;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function mergeXmlFiles(iterable $xmlFiles, OutputInterface $output): static
    {
        return $this
            ->start($output)
            ->addXmlFiles($xmlFiles)
            ->finish();
    }

    /**
     * {@inheritdoc}
     */
    public function mergeXmlStrings(\Iterator $xmlStrings, OutputInterface $output): static
    {
        return $this
            ->start($output)
            ->addXmlStrings($xmlStrings)
            ->finish();
    }

    public function start(OutputInterface $output): static
    {
        $this->output = $output;

        $this->output->writeln('<?xml version="1.0" encoding="UTF-8"?>');
        $this->output->writeln('<' . $this->getRootNodeName() . '>');

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addXmlFiles(\Iterator $xmlFiles): static
    {
        while ($xmlFiles->valid()) {
            $this->addXmlFile($xmlFiles->current());
            $xmlFiles->next();
        }

        return $this;
    }

    public function addXmlFile(string|\SplFileInfo $xmlFile): static
    {
        $filePath = $xmlFile instanceof \SplFileInfo
            ? $xmlFile->getPathname()
            : rtrim($xmlFile, "\r\n");

        $filePath = preg_replace(
            '@^/proc/self/fd/(?P<id>\d+)$@',
            'php://fd/$1',
            $filePath,
        );

        if ($filePath === '') {
            return $this;
        }

        $fileContent = file_get_contents($filePath);
        if ($fileContent === false) {
            // @todo Error logging.
            return $this;
        }

        $this->addXmlString($fileContent);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addXmlStrings(\Iterator $xmlStrings): static
    {
        while ($xmlStrings->valid()) {
            $this->addXmlString($xmlStrings->current());
            $xmlStrings->next();
        }

        return $this;
    }

    abstract public function addXmlString(string $xmlString): static;

    public function finish(): static
    {
        $this->output->writeln('</' . $this->getRootNodeName() . '>');

        return $this;
    }
}
