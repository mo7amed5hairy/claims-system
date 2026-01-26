<?php

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * Base Repository Class
 * 
 * Provides common CRUD operations for all repositories
 */
abstract class BaseRepository implements RepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * Constructor
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Get all records
     */
    public function all()
    {
        return $this->model->all();
    }

    /**
     * Get records with pagination
     */
    public function paginate($perPage = 15)
    {
        return $this->model->paginate($perPage);
    }

    /**
     * Get record by ID
     */
    public function find($id)
    {
        return $this->model->find($id);
    }

    /**
     * Create a new record
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Update a record
     */
    public function update($id, array $data)
    {
        $model = $this->find($id);
        
        if ($model) {
            $model->update($data);
            return $model;
        }
        
        return null;
    }

    /**
     * Delete a record
     */
    public function delete($id)
    {
        $model = $this->find($id);
        
        if ($model) {
            return $model->delete();
        }
        
        return false;
    }

    /**
     * Find by specific column
     */
    public function findBy($column, $value)
    {
        return $this->model->where($column, $value)->first();
    }

    /**
     * Get all filtered by conditions
     */
    public function where($column, $operator = null, $value = null)
    {
        return $this->model->where($column, $operator, $value);
    }

    /**
     * Get the model instance
     */
    public function getModel()
    {
        return $this->model;
    }
}
