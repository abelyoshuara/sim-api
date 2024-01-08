<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class Photos extends ResourceController
{
    protected $modelName = 'App\Models\MahasiswaModel';
    protected $format    = 'json';

    public function show($id = null)
    {
        $mahasiswa = $this->model->find($id);

        if (!$mahasiswa) {
            return $this->failNotFound(sprintf('mahasiswa with id %d not found', $id));
        }

        $path = WRITEPATH . 'uploads/mahasiswa/' . $mahasiswa->foto;

        if (!file_exists($path) || !$mahasiswa->foto) {
            return $this->failNotFound('image not found');
        }

        $mimeType = mime_content_type($path);
        $image = file_get_contents($path);

        $response = $this->response
            ->setStatusCode(200)
            ->setBody($image)
            ->setHeader('Content-Type', $mimeType);

        return $response;
    }
}
