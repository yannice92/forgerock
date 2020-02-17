<?php
/**
 * Samuelerwardi samuelerwardi@gmail.com
 */

/**
 * Samuelerwardi samuelerwardi@gmail.com
 */

namespace App\Forgerock\Repositories;

use App\Exceptions\RepositoryExceptions;
use App\Forgerock\Repositories\Interfaces\MemberRepositoryInterface;
use Illuminate\Container\Container as App;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Concrete;

/**
 * Class Repository
 * @package App\Repositories
 */
abstract class Repository implements MemberRepositoryInterface
{
    /** @var \Pimcore\Model\DataObject\Listing $listing */
    protected $listing;
    /**
     * @var App
     */
    private $app;

    /**
     * @var
     */
    protected $model;


    public function __construct(App $app)
    {
        $this->app = $app;
        $this->makeModel();
        $class = get_class($this->model) . "\\Listing";
        /** @var DataObject\Listing listing */
        $this->listing = new $class;
    }

    abstract function model();

    abstract function sort();


    public function makeModel()
    {
        $model = $this->app->make($this->model());

        if (!$model instanceof Concrete) {
            throw new RepositoryExceptions("Class {$this->model()} must be an instance of Pimcore\\Model\\DataObject\\Concrete");
        }

        return $this->model = $model;
    }

    public function all(array $columns = ['*'])
    {
        return $this->listing->load();
    }

    public function create(array $data): Concrete
    {
        try {
            return $this->model->save();
        } catch (\Exception $e) {
            dd($e->getMessage());
            die;
        }
    }

    public function paginate(int $perPage = 15, $columns = ['*'])
    {
        // TODO: Implement paginate() method.
    }

    public function updateBy(string $field, string $value, array $data)
    {
        // TODO: Implement updateBy() method.
    }

    public function delete(int $id)
    {
        // TODO: Implement delete() method.
    }

    public function find(int $id, $columns = array('*'))
    {
        // TODO: Implement find() method.
        $this->listing->setCondition("oo_id = ?", $id);
        return $this->listing->load();
    }

    public function findBy(string $field, string $value, $columns = ['*'])
    {
        // TODO: Implement findBy() method.
        $result = $this->listing->setCondition("{$field} = ?", [$value]);
        return $result->load();

    }

    public function update(array $data, array $id)
    {
        // TODO: Implement update() method.
    }
}
