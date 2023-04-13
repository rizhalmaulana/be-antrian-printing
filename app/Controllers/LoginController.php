<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\User;

class LoginController extends ResourceController
{
    use ResponseTrait;

    public function index()
    {
        helper(['form']);

        $modelName = new User();
        $rules = $this->validate([
            'input'    => 'required|valid_email',
            'password' => 'required|min_length[8]',
        ]);

        if (!$rules) {
            $response = [
                'message' => $this->validator->getErrors()
            ];

            return $this->failValidationErrors($response);
        }

        $login_email = $modelName->where('email_user', $this->request->getVar('input'))->first();

        if (!$login_email) {
            $response = [
                'status' => false,
                'code' => 404,
                'message' => 'Data email yang anda masukkan salah.'
            ];

            return $this->respond($response, 404);
        }

        $verify = password_verify($this->request->getVar('password'), $login_email['password']);
        
        if (!$verify) {
            $response = [
                'status' => false,
                'code' => 404,
                'message' => 'Data password yang anda masukkan salah.'
            ];

            return $this->respond($response, 404);
        }

        $response = [
            'status' => true,
            'code' => 200,
            'message' => 'Berhasil Login',
            'data' => $login_email
        ];

        return $this->respond($response, 200);
    }
}