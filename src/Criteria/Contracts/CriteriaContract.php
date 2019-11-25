<?php

namespace Redmix0901\Core\Criteria\Contracts;

use Redmix0901\Core\Repositories\Interfaces\RepositoryInterface;

interface CriteriaContract
{
    /**
     * @param \Eloquent $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository);
}
