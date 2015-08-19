<?php
/**
 * @link https://github.com/idzn/Martin2
 * @copyright Copyright (c) 2015, Sergei Tolokonnikov
 * @license https://github.com/idzn/Martin2/blob/master/LICENSE
 */

namespace Martin\components\Assets;

class Assets
{
    private $linkedAssets = [];
    private $realPath;
    private $fileName;
    private $fileExt;
    private $realPathHash;
    private $fileHash;
    private $assetLocalPath;
    private $assetRealPath;
    private $assetDirectoryPath;

    private $pathMode;

    public function __construct($config)
    {
        $this->pathMode = $config['pathMode'];
        @mkdir(ASSETS_PATH);
        @chmod(ASSETS_PATH, $this->pathMode);
    }

    private function getFileInfo($path)
    {
        $this->realPath = realpath($path);
        $this->fileName = basename($path);
        $this->fileExt = pathinfo($this->realPath, PATHINFO_EXTENSION);
        $this->realPathHash = md5($this->realPath);
        $this->fileHash = md5_file($this->realPath);
        $this->assetLocalPath = DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . $this->realPathHash .
            DIRECTORY_SEPARATOR . $this->fileHash . '.' . $this->fileName . '.' . $this->fileExt;
        $this->assetRealPath = realpath(ASSETS_PATH) . DIRECTORY_SEPARATOR . $this->realPathHash .
            DIRECTORY_SEPARATOR . $this->fileHash . '.' . $this->fileName . '.' . $this->fileExt;
        $this->assetDirectoryPath = dirname($this->assetRealPath);
    }

    private function createAsset()
    {
        if (!file_exists($this->assetDirectoryPath)) mkdir($this->assetDirectoryPath);
        copy($this->realPath, $this->assetRealPath);
    }

    private function deleteAsset()
    {

    }

    private function removeDir($dir)
    {
        if ($objs = glob($dir."/*")) {
            foreach($objs as $obj) {
                is_dir($obj) ? $this->removeDir($obj) : @unlink($obj);
            }
        }
        @rmdir($dir);
    }

    private function _link($path, $type)
    {
        $this->realPath = realpath($path);

        if (in_array($this->realPath, $this->linkedAssets)) return;
        if (!file_exists($this->realPath)) return;

        $this->getFileInfo($path);
        if (!file_exists($this->assetRealPath)) {
            $this->removeDir($this->assetDirectoryPath);
            if (!file_exists($this->assetDirectoryPath)) mkdir($this->assetDirectoryPath);
            chmod($this->assetDirectoryPath, $this->pathMode);
            copy($this->realPath, $this->assetRealPath);
            chmod($this->assetRealPath, $this->pathMode);
        }

        $this->linkedAssets[] = $this->realPath;

        switch ($type) {
            case 'css':
                echo '<link rel="stylesheet" href="' . $this->assetLocalPath . '">';
                break;
            case 'js':
                echo '<script src="' . $this->assetLocalPath . '"></script>';
                break;
        }

    }

    public function linkStylesheet($path)
    {
        $this->_link($path, 'css');
    }

    public function linkScript($path)
    {
        $this->_link($path, 'js');
    }

} 