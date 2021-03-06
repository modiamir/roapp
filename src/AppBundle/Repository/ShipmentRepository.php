<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Address;
use AppBundle\Entity\BusinessUnit;
use AppBundle\Entity\Shipment;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

/**
 * ShipmentRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ShipmentRepository extends EntityRepository
{
    /**
     * @param BusinessUnit $businessUnit
     * @return array
     */
    public function getShipmentsPerBusinessUnit(BusinessUnit $businessUnit)
    {
        $from = new \DateTime();
        $until = new \DateTime();
        $from = $from->setTime(00, 00, 00);
        $until = $until->setTime(23, 59, 59);
        $shipments =  $this->createQueryBuilder('s')
            ->select('s')
            ->join('s.ownerAddress', 'a')
            ->join('a.businessUnit', 'b')
            ->where('b.id=:bid')
            ->andWhere('s.createdAt >= :from')
            ->andwhere('s.createdAt <= :until')
            ->andWhere('s.customerDriver=:customer_driver')
            ->andWhere('s.status=:status')
            ->setParameter('bid', $businessUnit->getId())
            ->setParameter('from', $from)
            ->setParameter('until', $until)
            ->setParameter('customer_driver', false)
            ->setParameter('status', Shipment::STATUS_FINISH)
            ->getQuery()
            ->getResult();

        return $shipments;
    }

    /**
     * @param Address $address
     * @return bool
     */
    public function isShipmentExists(Address $address)
    {
        $result = $this->createQueryBuilder('s')
            ->select('COUNT(s)')
            ->where('s.ownerAddress=:owner_address')
            ->orWhere('s.otherAddress=:other_address')
            ->setParameter('owner_address', $address)
            ->setParameter('other_address', $address)
            ->getQuery()
            ->getSingleScalarResult();

        if ($result == 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param int $shipmentId
     * @return mixed|null
     */
    public function getShipmentCanCancel($shipmentId)
    {
        try {
            $result = $this->createQueryBuilder('s')
                ->where('s.id=:shipmentId')
                ->andWhere('s.status IN (:status)')
                ->setParameter('shipmentId', $shipmentId)
                ->setParameter(
                    'status',
                    [
                        Shipment::STATUS_NOT_ASSIGNED,
                        Shipment::STATUS_ASSIGNMENT_SENT,
                    ]
                )
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException $e) {
            $result = null;
        }

        return $result;
    }
}
