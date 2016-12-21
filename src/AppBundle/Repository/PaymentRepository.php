<?php

namespace AppBundle\Repository;

use AppBundle\Entity\AbstractInvoice;
use AppBundle\Entity\Payment;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

/**
 * PaymentRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PaymentRepository extends EntityRepository
{
    /**
     * @param int $paymentId
     * @return mixed
     * return payment that did not pay
     */
    public function checkPayment($paymentId)
    {
        $payment = $this->createQueryBuilder('p')
            ->where('p.id=:paymentId AND p.status=:waiting')
            ->orWhere('p.id=:paymentId AND p.status=:failed')
            ->setParameter('paymentId', $paymentId)
            ->setParameter('waiting', Payment::STATUS_WAITING_FOR_PAYMENT)
            ->setParameter('failed', Payment::STATUS_FAILED)
            ->getQuery()
            ->getSingleResult();

        return $payment;
    }

    /**
     * @param AbstractInvoice $invoice
     * @return mixed
     */
    public function findOpenPayment(AbstractInvoice $invoice)
    {
        try {
            $result = $this->createQueryBuilder("p")
                ->where("p.invoice=:invoice")
                ->andWhere('p.status IN (:status)')
                ->setParameter(
                    'status',
                    [
                        Payment::STATUS_WAITING_FOR_APPROVE,
                        Payment::STATUS_APPROVED,
                        Payment::STATUS_WAITING_FOR_PAYMENT,
                        Payment::STATUS_IN_PROGRESS,
                    ]
                )
                ->setParameter("invoice", $invoice)
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException $e) {
            $result = null;
        }

        return $result;
    }
}
