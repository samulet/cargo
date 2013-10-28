<?php
namespace Ticket\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;

class TicketRepository extends DocumentRepository
{
    public function getAllAvailableTicket()
    {
        return $this->createQueryBuilder()
            ->field('deletedAt')->equals(null)
            ->getQuery()->execute();
    }

    public function getMyAvailableTicket($owner_id)
    {
        return $this->createQueryBuilder()
            ->field('deletedAt')->equals(null)->field('ownerId')->equals(
                new \MongoId($owner_id)
            )
            ->getQuery()->execute();
    }
}