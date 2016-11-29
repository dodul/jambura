<?php
class jAssets {
    private $jsList = [];
    private $cssList = [];
    private $collection = 'default';
    private $root = ROOT;

    /**
     * Sets collection to add asset to
     *
     * Sets the value of collection so that assets can be grouped and
     * loaded into different parts of the template. (eg. Header or footer)
     *
     * @param string $collection Name of the colelction
     * @return object $this returns self object
     */
    public function in($collection) {
        $this->collection = $collection;
        return $this;
    }

    private function addAsset($file, &$list) {
        if ($this->isAddedBefore($list, $file)) {
            return $this;
        }

        if (!file_exists($file)) {
            throw new Exception('File does not exist');
        }

        $list[$this->collection][] = $this->root.'/'.$file;

        return $this;
    }

    /**
     * Add Javascript asset
     *
     * Add an external Javascript source file to the AssetManager
     *
     * @param string $file file path and name
     * @return object $this 
     * @throws Exception if file not found
     */
    public function addJS($file) {
        return $this->addAsset($file, $this->jsList);
    }

    /**
     * Add CSS asset
     *
     * Add an external CSS source file to the AssetManager
     *
     * @param string $file file path and name
     * @return object $this 
     * @throws Exception if file not found
     */
    public function addCSS($file) {
        return $this->addAsset($file, $this->cssList);
    }

    /**
     * Load Javascript files
     * 
     * Load all Javascript files listed under one perticular collection 
     * into the template from the AssetManager
     *
     * @param string $collection name of collection
     */
    public function loadJS($collection) {
        $this->loadAssets(
            $collection,
            $this->jsList,
            '<script src="',
            '"></script>'
        );
    }

    /**
     * Load CSS files
     * 
     * Load all Javascript files listed under one perticular collection 
     * into the template from the AssetManager
     *
     * @param string $collection name of collection
     */
    public function loadCSS($collection) {
        $this->loadAssets(
            $collection,
            $this->cssList,
            '<link rel="stylesheet" type="text/css" href="',
            '">'
        );
    }

    /**
     * Prints source tags on html file
     *
     * Prints the names and paths of assets listed under a given collection 
     * within given tags. This private method used by loadCSS and loadJS method.
     *
     * @param string $collection name of colection
     * @param array  $list       list of asset filenames to be printed
     * @param string $before     The first part of the tag before filename
     * @param string $after      The last part of the tag adter filename
     */
    private function loadAssets($collection, $list, $before, $after) {
        if (!isset($list[$collection])) {
            return;
        }
        echo "\n";
        foreach ($list[$collection] as $asset) {
            echo "$before$asset$after\n";
        }
    }

    /**
     * Checks if file name was added to list before
     *
     * @param array $list  List of filenames to check in
     * @param string $file Filename to check
     *
     * @return bool
     */
    private function isAddedBefore($list, $file) {
        if (!isset($list[$this->collection])) {
            return false;
        }

        if (in_array(ROOT.'/'.$file, $list[$this->collection])) {
            return true;
        }

        return false;
    }

    /**
     * set the root url for loading assets
     *
     * @param string $root root url
     */
    public function setRoot($root) {
        $this->root = $root;
    }
}
