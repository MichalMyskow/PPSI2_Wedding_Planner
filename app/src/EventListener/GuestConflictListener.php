<?php

namespace App\EventListener;

use App\Entity\Guest;
use Doctrine\ORM\Event\OnFlushEventArgs;

class GuestConflictListener
{
    public function onFlush(OnFlushEventArgs $args)
    {
        $entityManager = $args->getEntityManager();
        $uow = $entityManager->getUnitOfWork();
        $guestMetadata = $entityManager->getClassMetadata(Guest::class);

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof Guest) {
                $guestsConflictsToRemove = $entityManager->getRepository(Guest::class)->findByConflictedGuest($entity);

                foreach ($entity->getConflictedGuests() as $conflictedGuest) {
                    $conflictedGuest->addConflictedGuest($entity);
                    $uow->computeChangeSet($guestMetadata, $conflictedGuest);

                    foreach ($guestsConflictsToRemove as $key => $value) {
                        if ($value === $conflictedGuest) {
                            unset($guestsConflictsToRemove[$key]);
                            break;
                        }
                    }
                }

                foreach ($guestsConflictsToRemove as $conflictedGuest) {
                    /* @var Guest $conflictedGuest */
                    $conflictedGuest->removeConflictedGuest($entity);
                    $uow->computeChangeSet($guestMetadata, $conflictedGuest);
                }
            }
        }
    }
}
