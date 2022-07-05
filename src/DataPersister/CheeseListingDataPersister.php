<?php

namespace App\DataPersister;



use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\CheeseListing;
use App\Entity\CheeseNotification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CheeseListingDataPersister implements \ApiPlatform\Core\DataPersister\DataPersisterInterface
{
    /**
     * @var DataPersisterInterface
     */
    private $decoratedDataPersister;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(DataPersisterInterface $decoratedDataPersister, EntityManagerInterface $entityManager)
    {
        $this->decoratedDataPersister = $decoratedDataPersister;
        $this->entityManager = $entityManager;
    }

    /**
     * @param CheeseListing $data
     */
    public function supports($data): bool
    {
       return $data instanceof CheeseListing;
    }

    public function persist($data)
    {
        $originalData = $this->entityManager->getUnitOfWork()->getOriginalEntityData($data);
        $wasAlreadyPublished = ($originalData['isPublished'] ?? false);
        if ($data->getIsPublished() && !$wasAlreadyPublished){
            $notification = new CheeseNotification($data, 'Cheese listing was created!');
            $this->entityManager->persist($notification);
            $this->entityManager->flush();
        }

        return $this->decoratedDataPersister->persist($data);
    }

    public function remove($data)
    {
       return $this->decoratedDataPersister->remove($data);
    }

}