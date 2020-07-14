<?php

/**
 * NOTE: This class is auto generated by Tars Generator (https://github.com/wenbinye/tars-generator).
 *
 * Do not edit the class manually.
 * Tars Generator version: 1.0-SNAPSHOT
 */

namespace winwin\tars\file\servant;

use wenbinye\tars\protocol\annotation\TarsServant;
use wenbinye\tars\protocol\annotation\TarsParameter;
use wenbinye\tars\protocol\annotation\TarsReturnType;

/**
 * @TarsServant(name="TarsFileRegistryObj")
 */
interface TarsFileRegistryServant
{
    /**
     * @TarsParameter(name = "file", type = "TarsFile")
     * @TarsReturnType(type = "int")
     *
     * @param \winwin\tars\file\servant\TarsFile $file
     * @return int
     */
    public function add($file);

    /**
     * @TarsParameter(name = "packageName", type = "string")
     * @TarsParameter(name = "revision", type = "string")
     * @TarsReturnType(type = "vector<string>")
     *
     * @param string $packageName
     * @param string $revision
     * @return array
     */
    public function listFiles($packageName, $revision);

    /**
     * @TarsParameter(name = "packageName", type = "string")
     * @TarsParameter(name = "revision", type = "string")
     * @TarsParameter(name = "fileName", type = "string")
     * @TarsParameter(name = "limit", type = "int")
     * @TarsReturnType(type = "vector<int>")
     *
     * @param string $packageName
     * @param string $revision
     * @param string $fileName
     * @param int $limit
     * @return array
     */
    public function listVersions($packageName, $revision, $fileName, $limit);

    /**
     * @TarsParameter(name = "query", type = "TarsFileQuery")
     * @TarsReturnType(type = "string")
     *
     * @param \winwin\tars\file\servant\TarsFileQuery $query
     * @return string
     */
    public function getContent($query);

}