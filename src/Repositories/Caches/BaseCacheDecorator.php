<?php

namespace Redmix0901\Core\Repositories\Caches;

use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\Model;
use Redmix0901\Core\Criteria\Contracts\CriteriaContract;
use Redmix0901\Core\Repositories\Interfaces\BaseRepositoryInterface;
use Redmix0901\Core\Cache\Cache;

abstract class BaseCacheDecorator implements BaseRepositoryInterface
{
    /**
     * @var BaseRepositoryInterface
     */
    protected $repository;

    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @var cache_time
     */
    protected $cache_time = 10;
    
    /**
     * BaseCacheDecorator constructor.
     */
    public function __construct(BaseRepositoryInterface $repository, string $group = null)
    {
        $this->repository = $repository;
        $this->cache = new Cache(app('cache'), $group ?? get_class($repository));
    }

    /**
     * @return Cache
     */
    public function getCacheInstance()
    {
        return $this->cache;
    }

    /**
     * @param $function
     * @param array $args
     * @return mixed
     */
    public function getWithCache($function, array $args)
    {
        try { 
            $key = md5(
                get_class($this) .
                $function .
                serialize(request()->input()) . 
                serialize(url()->current()) .
                serialize(func_get_args())
            );

            if ($this->cache->has($key)) {
                try {
                    return $this->cache->get($key);
                }
                catch (\Exception $e) {
                }
            }

            $value = call_user_func_array([$this->repository, $function], $args);

            // Store in cache for next request
            $this->cache->put($key, $value, $this->cache_time);

            return $value;
        } catch (Exception $e) {
            info($e->getMessage());
            return call_user_func_array([$this->repository, $function], $args);
        }
    }

    /**
     * @param $function
     * @param array $args
     * @return mixed
     */
    public function getWithoutCache($function, array $args)
    {
        return call_user_func_array([$this->repository, $function], $args);
    }

    /**
     * @param $function
     * @param $args
     * @param boolean $isFlush
     * @return mixed
     */
    public function flushCache($function, $args, $isFlush = true)
    {
        if ($isFlush) {
            try {
                $this->cache->flush();
            } catch (FileNotFoundException $exception) {
                info($exception->getMessage());
            }
        }

        return call_user_func_array([$this->repository, $function], $args);
    }

    /**
     * {@inheritdoc}
     */
    public function getCriteria()
    {
        return $this->getWithoutCache(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function pushCriteria(CriteriaContract $criteria)
    {
        $this->getWithoutCache(__FUNCTION__, func_get_args());
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function dropCriteria($criteria)
    {
        $this->getWithoutCache(__FUNCTION__, func_get_args());
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function skipCriteria($bool = true)
    {
        $this->getWithoutCache(__FUNCTION__, func_get_args());
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function applyCriteria()
    {
        $this->getWithoutCache(__FUNCTION__, func_get_args());
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getByCriteria(CriteriaContract $criteria)
    {
        return $this->getWithCache(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function getModel()
    {
        return $this->repository->getModel();
    }

    /**
     * {@inheritdoc}
     */
    public function setModel($model)
    {
        return $this->repository->setModel($model);
    }

    /**
     * {@inheritdoc}
     */
    public function getTable()
    {
        return $this->repository->getTable();
    }

    /**
     * {@inheritdoc}
     */
    public function getScreen(): string
    {
        return $this->repository->getScreen();
    }

    /**
     * {@inheritdoc}
     */
    public function applyBeforeExecuteQuery($data, $screen, $is_single = false)
    {
        return $this->repository->applyBeforeExecuteQuery($data, $screen, $is_single);
    }

    /**
     * {@inheritdoc}
     */
    public function make(array $with = [])
    {
        return $this->repository->make($with);
    }

    /**
     * {@inheritdoc}
     */
    public function findById($id, array $with = [])
    {
        return $this->getWithCache(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function findOrFail($id, array $with = [])
    {
        return $this->getWithCache(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstBy(array $condition = [], array $select = [], array $with = [])
    {
        return $this->getWithCache(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function pluck($column, $key = null)
    {
        return $this->getWithCache(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function all(array $with = [])
    {
        return $this->getWithCache(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function allBy(array $condition, array $with = [], array $select = ['*'])
    {
        return $this->getWithCache(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data)
    {
        return $this->flushCache(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function createOrUpdate($data, $condition = [])
    {
        return $this->flushCache(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Model $model)
    {
        return $this->flushCache(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function firstOrCreate(array $data, array $with = [])
    {
        return $this->flushCache(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function update(array $condition, array $data)
    {
        return $this->flushCache(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function select(array $select = ['*'], array $condition = [])
    {
        return $this->getWithoutCache(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function deleteBy(array $condition = [])
    {
        return $this->flushCache(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function count(array $condition = [])
    {
        return $this->getWithCache(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function getByWhereIn($column, array $value = [], array $args = [])
    {
        return $this->getWithCache(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function advancedGet(array $params = [])
    {
        return $this->getWithCache(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function forceDelete(array $condition = [])
    {
        return $this->flushCache(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function restoreBy(array $condition = [])
    {
        return $this->flushCache(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstByWithTrash(array $condition = [], array $select = [])
    {
        return $this->getWithCache(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function insert(array $data)
    {
        return $this->flushCache(__FUNCTION__, func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function firstOrNew(array $condition)
    {
        return $this->getWithCache(__FUNCTION__, func_get_args());
    }
}
