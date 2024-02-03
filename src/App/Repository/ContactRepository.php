<?php

declare(strict_types=1);

namespace App\Repository;

use App\Domain;
use Doctrine\ORM\EntityRepository;

/**
 * @extends EntityRepository<Domain\Contact>
 */
class ContactRepository extends EntityRepository
{
    /**
     * @return array<int, Domain\Contact>
     */
    public function search(string $query): array
    {
        $dql = $this
            ->createQueryBuilder('c')
            ->where('c.first LIKE :query OR c.last LIKE :query')
            ->getDQL();

        $query = $this
            ->getEntityManager()
            ->createQuery($dql)
            ->setParameter('query', "%{$query}%");

        /** @var array<int, Domain\Contact> */
        $result = $query->getResult();
        return $result;
    }
}
