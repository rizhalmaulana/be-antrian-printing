<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function antrian()
    {
        $data = [
            'title' => "Antrian | Antrian Printing"
        ];

        echo view('component/header', $data);
        echo view('home/antrian');
        echo view('component/footer');
    }

    public function tentang()
    {
        $data = [
            'title' => "Tentang | Antrian Printing"
        ];

        echo view('component/header', $data);
        echo view('home/tentang');
        echo view('component/footer');
    }
}
