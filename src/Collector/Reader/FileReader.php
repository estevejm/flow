<?php

namespace EJM\Flow\Collector\Reader;

class FileReader implements Reader
{
    /**
     * @param string $filename
     * @return string
     */
    public function read($filename)
    {
        return file_get_contents($filename);
    }
}
