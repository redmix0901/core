<?php

namespace Redmix0901\Core\Repositories\Interfaces;

use Redmix0901\Core\Criteria\AbstractCriteria;
use Redmix0901\Core\Criteria\Contracts\CriteriaContract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;

interface BaseRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getScreen(): string;

    /**
     * @param $data
     * @param $screen
     * @param bool $is_single
     * @return Builder
     */
    public function applyBeforeExecuteQuery($data, $screen, $is_single = false);

    /**
     * {@inheritdoc}
     */
    public function getCriteria();

    /**
     * {@inheritdoc}
     */
    public function pushCriteria(CriteriaContract $criteria);

    /**
     * {@inheritdoc}
     */
    public function dropCriteria($criteria);

    /**
     * {@inheritdoc}
     */
    public function skipCriteria($bool = true);

    /**
     * {@inheritdoc}
     */
    public function applyCriteria();

    /**
     * {@inheritdoc}
     */
    public function getByCriteria(CriteriaContract $criteria);

    /**
     * {@inheritdoc}
     */
    public function setModel($model);

    /**
     * {@inheritdoc}
     */
    public function getModel();

    /**
     * {@inheritdoc}
     */
    public function getTable();

    /**
     * {@inheritdoc}
     */
    public function make(array $with = []);

    /**
     * {@inheritdoc}
     */
    public function getFirstBy(array $condition = [], array $select = [], array $with = []);

    /**
     * {@inheritdoc}
     */
    public function findById($id, array $with = []);

    /**
     * {@inheritdoc}
     */
    public function findOrFail($id, array $with = []);

    /**
     * {@inheritdoc}
     */
    public function pluck($column, $key = null);

    /**
     * {@inheritdoc}
     */
    public function all(array $with = []);

    /**
     * {@inheritdoc}
     */
    public function allBy(array $condition, array $with = [], array $select = ['*']);

    /**
     * {@inheritdoc}
     */
    public function create(array $data);

    /**
     * {@inheritdoc}
     */
    public function createOrUpdate($data, $condition = []);

    /**
     * {@inheritdoc}
     */
    public function delete(Model $model);

    /**
     * {@inheritdoc}
     */
    public function firstOrCreate(array $data, array $with = []);

    /**
     * {@inheritdoc}
     */
    public function update(array $condition, array $data);

    /**
     * {@inheritdoc}
     */
    public function select(array $select = ['*'], array $condition = []);

    /**
     * {@inheritdoc}
     */
    public function deleteBy(array $condition = []);

    /**
     * {@inheritdoc}
     */
    public function count(array $condition = []);

    /**
     * {@inheritdoc}
     */
    public function getByWhereIn($column, array $value = [], array $args = []);

    /**
     * @param array $params
     * @return LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection|Collection|mixed
     */
    public function advancedGet(array $params = []);

    /**
     * {@inheritdoc}
     */
    public function forceDelete(array $condition = []);

    /**
     * {@inheritdoc}
     */
    public function restoreBy(array $condition = []);

    /**
     * {@inheritdoc}
     */
    public function getFirstByWithTrash(array $condition = [], array $select = []);

    /**
     * {@inheritdoc}
     */
    public function insert(array $data);

    /**
     * {@inheritdoc}
     */
    public function firstOrNew(array $condition);
}
