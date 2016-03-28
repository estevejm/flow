<?php

namespace Flow\Collector\Reader;

class FileReader
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
