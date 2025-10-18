<?php

declare(strict_types = 1);

namespace Sweetchuck\JunitMerger;

use Symfony\Component\Console\Output\OutputInterface;

interface JunitMergerInterface
{

    public function getRootNodeName(): string;

    public function setRootNodeName(string $rootNodeName): static;

    /**
     * @param \Iterator<string|\SplFileInfo> $xmlFiles
     */
    public function mergeXmlFiles(\Iterator $xmlFiles, OutputInterface $output): static;

    /**
     * @param \Iterator<string> $xmlStrings
     */
    public function mergeXmlStrings(\Iterator $xmlStrings, OutputInterface $output): static;

    public function start(OutputInterface $output): static;

    /**
     * @param \Iterator<string|\SplFileInfo> $xmlFiles
     */
    public function addXmlFiles(\Iterator $xmlFiles): static;

    public function addXmlFile(string|\SplFileInfo $xmlFile): static;

    /**
     * @param \Iterator<string> $xmlStrings
     */
    public function addXmlStrings(\Iterator $xmlStrings): static;

    public function addXmlString(string $xmlString): static;

    public function finish(): static;
}
