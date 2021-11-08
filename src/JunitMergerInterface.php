<?php

declare(strict_types = 1);

namespace Sweetchuck\JunitMerger;

use Symfony\Component\Console\Output\OutputInterface;

interface JunitMergerInterface
{

    public function getRootNodeName(): string;

    /**
     * @return $this
     */
    public function setRootNodeName(string $rootNodeName);

    /**
     * @param string[]|\SplFileInfo[]|\Iterator $xmlFiles
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return $this
     */
    public function mergeXmlFiles(\Iterator $xmlFiles, OutputInterface $output);

    /**
     * @param string[] $xmlStrings
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return $this
     */
    public function mergeXmlStrings(\Iterator $xmlStrings, OutputInterface $output);

    /**
     * @return $this
     */
    public function start(OutputInterface $output);

    /**
     * @return $this
     */
    public function addXmlFiles(\Iterator $xmlFiles);

    /**
     * @param string|\SplFileInfo $xmlFile
     *
     * @return $this
     */
    public function addXmlFile($xmlFile);

    /**
     * @return $this
     */
    public function addXmlStrings(\Iterator $xmlStrings);

    /**
     * @return $this
     */
    public function addXmlString(string $xmlString);

    /**
     * @return $this
     */
    public function finish();
}
