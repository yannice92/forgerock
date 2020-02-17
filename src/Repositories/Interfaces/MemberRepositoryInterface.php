<?php
/**
 * Samuelerwardi samuelerwardi@gmail.com
 */
namespace App\Forgerock\Repositories\Interfaces;
use Pimcore\Model\DataObject\Concrete;

interface MemberRepositoryInterface
{
    public function update(array $data, array $id);
    public function all(array $columns = ['*']);

    public function create(array $data): Concrete;

    public function paginate(int $perPage = 15, $columns = ['*']);

    public function updateBy(string $field, string $value, array $data);

    public function delete(int $id);

    public function find(int $id, $columns = array('*'));

    public function findBy(string $field, string $value, $columns = ['*']);

}
