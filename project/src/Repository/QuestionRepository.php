<?php

namespace App\Repository;

use App\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Question>
 */
class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private readonly EntityManagerInterface $em)
    {
        parent::__construct($registry, Question::class);
    }

    public function findQuestions(array $criteria = []): array
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select("q");
        $qb->from(Question::class, "q", "q.id");
        if (isset($criteria["needRandomSorting"])) {
            $qb->orderBy("RANDOM()");
        }
        if (!empty($criteria["ids"])) {
            $qb->where($qb->expr()->in("q.id", $criteria["ids"]));
        }

        return $qb->getQuery()->getResult();
    }
}
