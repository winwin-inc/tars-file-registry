<?php


namespace winwin\tars\file\domain;


use kuiper\db\AbstractCrudRepository;
use kuiper\db\annotation\Entity;
use kuiper\di\annotation\Repository;

/**
 * @Entity(TarsFile::class)
 * @Repository()
 *
 * @method TarsFile findFirstBy($criteria);
 * @method TarsFile[] findAllBy($criteria);
 */
class TarsFileRepository extends AbstractCrudRepository
{

}