<div class="container position-sticky z-index-sticky top-0">
    <div class="row">
        <div class="col-12">
            <!-- Navbar -->
            <nav
                class="navbar navbar-expand-lg blur border-radius-lg top-0 z-index-3 shadow position-absolute mt-4 py-2 start-0 end-0 mx-4">
                <div class="container-fluid">
                    <a class="navbar-brand font-weight-bolder ms-lg-0 ms-3 " href="../pages/dashboard.html">
                        Antrian Printing
                    </a>
                    <button class="navbar-toggler shadow-none ms-2" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navigation" aria-controls="navigation" aria-expanded="false"
                        aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon mt-2">
                            <span class="navbar-toggler-bar bar1"></span>
                            <span class="navbar-toggler-bar bar2"></span>
                            <span class="navbar-toggler-bar bar3"></span>
                        </span>
                    </button>
                    <div class="collapse navbar-collapse" id="navigation">
                        <ul class="navbar-nav mx-auto">
                            <li class="nav-item">
                                <a class="nav-link d-flex align-items-center me-2 active" aria-current="page"
                                    href="/antrian">
                                    <i class="fas fa-users opacity-6 text-dark me-1"></i>
                                    Antrian
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link me-2" href="/tentang">
                                    <i class="fas fa-info-circle opacity-6 text-dark me-1"></i>
                                    Tentang
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
            <!-- End Navbar -->
        </div>
    </div>
</div>
<main class="main-content  mt-0">
    <section>
        <div class="page-header min-vh-100">
            <div class="container">
                <div class="row">
                    <form action="" method="POST">
                        <div class="col-xl-4 col-lg-5 col-md-7 d-flex flex-column mx-lg-0 mx-auto">
                            <div class="card card-plain">
                                <div class="card-header pb-0 text-start">
                                <?php if (session()->getFlashdata('error')) : ?>
                                    <div class="alert alert-info text-white" role="alert">
                                        <?= session()->getFlashdata('error'); ?>
                                    </div>
                                <?php endif; ?>
                                    <h4 class="font-weight-bolder">Login</h4>
                                    <p class="mb-0">Masukkan email kamu dan password untuk masuk</p>
                                </div>
                                <div class="card-body">
                                    <form role="form">
                                        <div class="mb-3">
                                            <input type="email" name="input" class="form-control form-control-lg" placeholder="Email"
                                                aria-label="Email">
                                        </div>
                                        <div class="mb-3">
                                            <input type="password" name="password" class="form-control form-control-lg"
                                                placeholder="Password" aria-label="Password">
                                        </div>
                                        <div class="text-center">
                                            <input type="submit" name="login"
                                                class="btn btn-lg btn-primary btn-lg w-100 mt-4 mb-0" value="Masuk" />
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div
                        class="col-6 d-lg-flex d-none h-100 my-auto pe-0 position-absolute top-0 end-0 text-center justify-content-center flex-column">
                        <div class="position-relative bg-gradient-secondary h-100 m-3 px-7 border-radius-lg d-flex flex-column justify-content-center overflow-hidden"
                            style="background-image: url('https://raw.githubusercontent.com/creativetimofficial/public-assets/master/argon-dashboard-pro/assets/img/signin-ill.jpg'); background-size: cover;">
                            <span class="mask bg-gradient-secondary opacity-6"></span>
                            <h4 class="mt-5 text-white font-weight-bolder position-relative">Quote Hari Ini</h4>
                            <p class="text-white position-relative">"Apa pun yang kita tunggu, ketenangan pikiran,
                                kepuasan, rahmat, kesadaran batin akan kelimpahan sederhana, pasti akan datang kepada
                                kita, hanya jika kita siap menerimanya dengan hati yang terbuka dan bersyukur." - Sarah
                                Ban Breathnach</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>