<?php

namespace Arthem\GraphQLMapper\Mapping\Driver;

abstract class FileDriver implements DriverInterface
{
    /**
     * @var FilePathAccessorInterface
     */
    private $pathAccessor;

    /**
     * @param array|string|FilePathAccessorInterface $files
     */
    public function __construct($files)
    {
        if ($files instanceof FilePathAccessorInterface) {
            $this->pathAccessor = $files;
        } else {
            $this->pathAccessor = new DefaultFilePathAccessor((array)$files);
        }
    }

    /**
     * @param string $path
     * @return string
     */
    protected function getFileContent($path)
    {
        if (!is_file($path)) {
            throw new \Exception(sprintf('File "%s" not found', $path));
        }

        return file_get_contents($path);
    }

    /**
     * @return array
     */
    protected function getPaths()
    {
        return $this->pathAccessor->getPaths();
    }
}
