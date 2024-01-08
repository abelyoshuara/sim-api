<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class Mahasiswa extends ResourceController
{
    protected $modelName = 'App\Models\MahasiswaModel';
    protected $format    = 'json';

    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return ResponseInterface
     */
    public function index()
    {
        return $this->respond([
            'status' => 'success',
            'data' => [
                'mahasiswa' => $this->model->findAll()
            ]
        ]);
    }

    /**
     * Return the properties of a resource object
     *
     * @return ResponseInterface
     */
    public function show($id = null)
    {
        $record = $this->model->find($id);

        if (!$record) {
            return $this->failNotFound(sprintf('mahasiswa with id %d not found', $id));
        }

        return $this->respond([
            'status' => 'success',
            'data' => [
                'mahasiswa' => $record
            ]
        ]);
    }

    /**
     * Return a new resource object, with default properties
     *
     * @return ResponseInterface
     */
    public function new()
    {
        //
    }

    /**
     * Create a new resource object, from "posted" parameters
     *
     * @return ResponseInterface
     */
    public function create()
    {
        $data = $this->request->getPost();

        if ($this->request->getFile('foto')) {
            $file = $this->request->getFile('foto');
            $newName = $file->getRandomName();
            $file->move(WRITEPATH . 'uploads/mahasiswa', $newName);
            $data['foto'] = $newName;
        }

        if (!$this->model->save($data)) {
            return $this->fail($this->model->errors());
        }

        return $this->respondCreated([
            'status' => 'success',
            'message' => 'mahasiswa created'
        ]);
    }

    /**
     * Return the editable properties of a resource object
     *
     * @return ResponseInterface
     */
    public function edit($id = null)
    {
        //
    }

    /**
     * Add or update a model resource, from "posted" properties
     *
     * @return ResponseInterface
     */
    public function update($id = null)
    {
        $data = $this->request->getRawInput();

        if (!$this->model->update($id, $data)) {
            return $this->fail($this->model->errors());
        }

        return $this->respond([
            'status' => 'success',
            'message' => 'mahasiswa updated'
        ], 200);
    }

    /**
     * Delete the designated resource object from the model
     *
     * @return ResponseInterface
     */
    public function delete($id = null)
    {
        $this->model->delete($id);

        if ($this->model->db->affectedRows() === 0) {
            return $this->failNotFound(sprintf('mahasiswa with id %d not found', $id));
        }

        return $this->respondDeleted([
            'status' => 'success',
            'message' => 'mahasiswa deleted'
        ]);
    }
}
