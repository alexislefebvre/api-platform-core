<?php

/*
 * This file is part of the API Platform project.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace ApiPlatform\Tests\Fixtures\TestBundle\ApiResource;

use ApiPlatform\Doctrine\Orm\Filter\PartialSearchFilter;
use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\QueryParameter;
use ApiPlatform\Tests\Fixtures\TestBundle\Entity\FilterWithStateOptionsAndNoApiFilterEntity;

#[ApiResource(
    stateOptions: new Options(entityClass: FilterWithStateOptionsAndNoApiFilterEntity::class),
    operations: [
        new GetCollection(
            parameters: [
                'search[:property]' => new QueryParameter(
                    properties: ['dummyDate', 'name'],
                    filter: new PartialSearchFilter(),
                ),
            ],
            provider: CollectionProvider::class
        ),
    ]
)]
final class FilterWithStateOptionsAndNoApiFilter
{
}
