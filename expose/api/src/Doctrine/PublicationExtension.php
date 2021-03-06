<?php

declare(strict_types=1);

namespace App\Doctrine;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Publication;
use Doctrine\ORM\QueryBuilder;

class PublicationExtension implements QueryCollectionExtensionInterface
{
    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        if (Publication::class === $resourceClass) {
            $rootAlias = $queryBuilder->getRootAliases()[0];
            $queryBuilder->andWhere(sprintf('%s.publiclyListed = true', $rootAlias));
            $queryBuilder->andWhere(sprintf('%s.enabled = true', $rootAlias));
            $queryBuilder->andWhere(sprintf('%s.parent IS NULL', $rootAlias));
            $queryBuilder->addOrderBy(sprintf('%s.title', $rootAlias), 'ASC');
        }
    }
}
