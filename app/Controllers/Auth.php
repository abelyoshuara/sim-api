<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Auth extends BaseController
{
    use ResponseTrait;

    public function login()
    {
        $userModel = model(UserModel::class);

        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');

        $user = $userModel->where('email', $email)->first();

        if (is_null($user)) {
            return $this->failNotFound('Invalid email or password.');
        }

        $pwd_verify = password_verify($password, $user->password);

        if (!$pwd_verify) {
            return $this->fail('Invalid username or password.', 401);
        }

        $key = getenv('JWT_SECRET');
        $iat = time();
        $exp = $iat + 3600;

        $payload = array(
            "iat" => $iat,
            "exp" => $exp,
            "uid" => $user->id,
            "name" => $user->name,
            "email" => $user->email,
        );

        $token = JWT::encode($payload, $key, 'HS256');

        return $this->respond([
            'status' => 'success',
            'message' => 'User logged succesfully',
            'data' => [
                'accessToken' => $token
            ]
        ], 200);
    }

    public function register()
    {
        $data = [
            'name'    => $this->request->getVar('name'),
            'email'    => $this->request->getVar('email'),
            'password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT)
        ];
        $userModel = model(UserModel::class);

        $user = new \App\Entities\User($data);
        if (!$userModel->save($user)) {
            return $this->fail($userModel->errors());
        }

        return $this->respondCreated([
            'status' => 'success',
            'message' => 'User created'
        ]);
    }

    public function me()
    {
        $key = getenv('JWT_SECRET');

        $header = $this->request->getServer('HTTP_AUTHORIZATION');
        if (!$header) return $this->failUnauthorized('Token required!');

        $token = explode(' ', $header)[1];

        try {
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            return $this->respond([
                'status' => 'success',
                'message' => 'User retrieved',
                'data' => [
                    'id' => $decoded->uid,
                    'name' => $decoded->name,
                    'email' => $decoded->email,
                ]
            ], 200);
        } catch (\Throwable $th) {
            return $this->fail('Invalid token!');
        }
    }
}
