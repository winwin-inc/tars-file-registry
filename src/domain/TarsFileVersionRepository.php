<?php


namespace winwin\tars\file\domain;


use kuiper\db\AbstractCrudRepository;
use kuiper\db\annotation\Entity;
use kuiper\di\annotation\Repository;

/**
 * @Entity(TarsFileVersion::class)
 * @Repository()
 *
 * @method TarsFileVersion findFirstBy($criteria);
 */
class TarsFileVersionRepository extends AbstractCrudRepository
{

}