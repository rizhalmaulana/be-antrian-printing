<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\User;
use App\Models\Admin;

class LoginController extends ResourceController {
    use ResponseTrait;
    
    public function index() {
        helper( [ 'form' ] );
        
        $data = [
            'title' => 'Login | Antrian Printing'
        ];
        
        $modelName = new Admin();
        
        $login = $this->request->getPost( 'login' );
        
        if ($login) {
            $email = $this->request->getVar( 'input' );
            $passwrd = $this->request->getVar( 'password' );
            
            if ( $email == '' or $passwrd == '' ) {
                $err = 'Silahkan masukkan email dan password!';
                
                session()->setFlashdata( 'error', $err );
                return redirect()->to( '/' );
            }
            
            $user = $modelName->where( 'email_admin', $email )->first();
            
            if ( !$user ) {
                $err = 'Email yang kamu masukkan salah!';
                
                session()->setFlashdata( 'error', $err );
                return redirect()->to('/');
            }
            
            $verify = password_verify( $this->request->getVar( 'password' ), $user[ 'password' ] );
            
            if ( !$verify ) {
                $err = 'Password yang kamu masukkan salah!';
                
                session()->setFlashdata( 'error', $err );
                return redirect()->to( '/' );
            }
            
            session()->set('user_id', $user['id']);
            session()->set('nama_lengkap', $user['nama_lengkap']);
            session()->set('username', $user['username']);
            session()->set('email_admin', $user['email_admin']);
            session()->set('status', $user['status']);
            
            return redirect()->to( '/dashboard' );
        }
        
        echo view( 'component/header', $data );
        echo view( 'auth/login' );
        echo view( 'component/footer' );
    }
    
    public function signin() {
        helper( [ 'form' ] );
        
        $modelName = new User();
        $rules = $this->validate( [
            'input'    => 'required|valid_email',
            'password' => 'required|min_length[8]',
            ] );
            
            if ( !$rules ) {
                $err = 'Silahkan masukkan email dan password!';
                session()->setFlashdata( 'error', $err );

                return redirect()->to( '/' );
            }
            
            $login_email = $modelName->where( 'email_user', $this->request->getVar( 'input' ) )->first();
            
            if ( !$login_email ) {
                $err = 'Data email yang anda masukkan salah!';
                session()->setFlashdata( 'error', $err );

                return redirect()->to( '/' );
            }
            
            $verify = password_verify( $this->request->getVar( 'password' ), $login_email[ 'password' ] );
            
            if ( !$verify ) {
                $err = 'Data password yang anda masukkan salah!';
                session()->setFlashdata( 'error', $err );

                return redirect()->to( '/' );
            }
            
            $response = [
                'status' => true,
                'code' => 200,
                'message' => 'Berhasil Login',
                'data' => $login_email
            ];
            
            return $this->respond( $response, 200 );
        }
    }