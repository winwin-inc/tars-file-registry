<?php

/**
 * NOTE: This class is auto generated by Tars Generator (https://github.com/wenbinye/tars-generator).
 *
 * Do not edit the class manually.
 * Tars Generator version: 1.0-SNAPSHOT
 */

namespace winwin\tars\file\servant;

use wenbinye\tars\protocol\annotation\TarsProperty;

final class TarsFile
{
    /**
     * @TarsProperty(order = 0, required = true, type = "string")
     * @var string
     */
    public $packageName;

    /**
     * @TarsProperty(order = 1, required = true, type = "string")
     * @var string
     */
    public $revision;

    /**
     * @TarsProperty(order = 2, required = true, type = "string")
     * @var string
     */
    public $fileName;

    /**
     * @TarsProperty(order = 3, required = true, type = "string")
     * @var string
     */
    public $content;

}
