<?php


namespace winwin\tars\file\domain;


use kuiper\db\AbstractCrudRepository;
use kuiper\db\annotation\Entity;
use kuiper\di\annotation\Repository;

/**
 * @Entity(TarsFileContent::class)
 * @Repository()
 *
 * @method TarsFileContent findById($id);
 */
class TarsFileContentRepository extends AbstractCrudRepository
{

}