<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>EDUVA | POLSA</title>
        <link rel="icon" type="image/png" href="{{ asset('images/eduva/eduva-logo.png') }}">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            html { scroll-behavior: smooth; }

            body { background: #fff; }



            .landing-nav {
                position: fixed;
                top: 0; left: 0; right: 0;
                z-index: 50;
                background: rgba(255,255,255,0.9);
                backdrop-filter: blur(8px);
                border-bottom: 1px solid #f1f5f9;
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 0 2rem;
                height: 60px;
                transition: box-shadow 0.15s;
            }

            .landing-nav.scrolled { box-shadow: 0 1px 8px rgba(0,0,0,0.06); }

            .landing-nav .logo {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                font-weight: 700;
                font-size: 1rem;
                color: #A16207;
            }

            .landing-nav .logo img {
                height: 20px;
                width: auto;
                max-width: 140px;
                object-fit: contain;
            }

            .landing-nav .nav-links { display: flex; gap: 0.25rem; }

            .landing-nav .nav-links a {
                font-size: 0.85rem;
                color: #94a3b8;
                font-weight: 500;
                padding: 0.4rem 0.85rem;
                border-radius: 6px;
                transition: all 0.15s;
            }

            .landing-nav .nav-links a:hover {
                background: #FFF3C4;
                color: #A16207;
            }

            .landing-nav .nav-login {
                margin-left: 1rem;
                background: #FEC200;
                color: #0A0D40 !important;
                font-weight: 600 !important;
                padding: 0.4rem 1.1rem !important;
            }

            .landing-nav .nav-login:hover {
                background: #D9A500 !important;
                color: #0A0D40 !important;
            }

            .landing-nav .nav-login-outline {
                margin-left: 1rem;
                background: transparent;
                color: #A16207 !important;
                font-weight: 600 !important;
                padding: 0.4rem 1.1rem !important;
                border: 1.5px solid #D9A500;
            }

            .landing-nav .nav-login-outline:hover {
                background: #FFF3C4 !important;
            }

            .landing-hero {
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 5rem 2rem 3rem;
                background: #fff;
            }

            .landing-hero .hero-inner {
                display: flex;
                align-items: center;
                gap: 3rem;
                max-width: 1000px;
                width: 100%;
            }

            .landing-hero .hero-text {
                flex: 1;

            }

            .landing-hero .hero-text h1 {
                font-size: 2.75rem;
                font-weight: 800;
                color: #0f172a;
                margin-bottom: 1rem;
                line-height: 1.15;
            }

            .landing-hero .hero-text .highlight { color: #FEC200; font-family: 'Batica Sans', sans-serif; font-weight: 700; }

            @font-face {
                font-family: 'Batica Sans';
                src: url('{{ asset('fonts/BaticaSans-Bold.ttf') }}') format('truetype');
                font-weight: 700;
                font-style: normal;
                font-display: swap;
            }

            .landing-hero .hero-text .hero-tagline {
                font-size: 0.95rem;
                font-weight: 600;
                letter-spacing: 0.18em;
                text-transform: uppercase;
                color: #A16207;
                margin: -0.35rem 0 1.25rem;
                line-height: 1.6;
            }

            .landing-hero .hero-text p {
                font-size: 1rem;
                color: #94a3b8;
                max-width: 460px;
                margin-bottom: 1.75rem;
                line-height: 1.7;
            }

            .landing-hero .hero-text .btn {
                background: #FEC200;
                color: #0A0D40;
                font-weight: 600;
                padding: 0.65rem 1.75rem;
                font-size: 0.9rem;
                border-radius: 8px;
            }

            .landing-hero .hero-text .btn:hover {
                background: #D9A500;
            }

            @keyframes float {
                0%, 100% { transform: translateY(0); }
                50% { transform: translateY(-12px); }
            }

            .landing-hero .hero-image {
                flex: 1;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .landing-hero .hero-image .float-wrap {
                animation: float 3s ease-in-out infinite;
            }

            .landing-hero .hero-image .hero-img {
                max-width: 240px;
                width: 100%;
                height: auto;
                object-fit: contain;
                filter: drop-shadow(0 20px 30px rgba(254, 194, 0, 0.12));
            }

            .landing-section {
                padding: 4rem 2rem;
                max-width: 1000px;
                margin: 0 auto;
                opacity: 0;
                transform: translateY(30px);
                transition: opacity 0.2s ease, transform 0.2s ease;
            }

            .landing-section.visible {
                opacity: 1;
                transform: translateY(0);
            }

            .landing-section h2 {
                font-size: 1.75rem;
                font-weight: 700;
                text-align: center;
                margin-bottom: 0.35rem;
                color: #0f172a;
            }

            .landing-section .section-subtitle {
                text-align: center;
                color: #94a3b8;
                margin-bottom: 2.5rem;
                font-size: 0.9rem;
            }

            .landing-section-alt { background: #fafbfc; }

            .kampus-card {
                background: #fff;
                border-radius: 14px;
                border: 1px solid #f1f5f9;
                overflow: hidden;
                display: flex;
                align-items: stretch;
            }

            .kampus-img {
                flex-shrink: 0;
                width: 300px;
                display: flex;
                align-items: center;
                justify-content: center;
                background: #f8faff;
                padding: 1rem;
            }

            .kampus-img img {
                width: 100%;
                height: auto;
                display: block;
                border-radius: 8px;
            }

            .kampus-body {
                flex: 1;
                padding: 1.5rem 1.5rem 2rem;
            }

            @media (max-width: 768px) {
                .kampus-card { flex-direction: column; }
                .kampus-img { width: 100%; padding: 1.5rem; }
                .kampus-img img { max-height: 220px; object-fit: contain; }
                .kampus-body { padding: 1rem 1rem 1.25rem; }
            }

            .vm-grid {
                display: flex;
                flex-direction: column;
                gap: 1.25rem;
            }

            .vm-card {
                background: #fff;
                border-radius: 14px;
                border: 1px solid #f1f5f9;
                overflow: hidden;
                transition: box-shadow 0.2s;
            }

            .vm-card:hover {
                box-shadow: 0 4px 20px rgba(0,0,0,0.04);
            }

            .vm-card-head {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                padding: 1rem 1.5rem;
                background: linear-gradient(135deg, #f8faff, #FFF3C4);
                border-bottom: 1px solid #f1f5f9;
            }

            .vm-card-head .vm-badge {
                flex-shrink: 0;
                width: 2.25rem;
                height: 2.25rem;
                border-radius: 8px;
                background: linear-gradient(135deg, #FEC200, #FFD54F);
                color: #fff;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 0.85rem;
                font-weight: 700;
            }

            .vm-card-head h3 {
                font-size: 1rem;
                font-weight: 700;
                color: #0f172a;
                margin: 0;
            }

            .vm-card-body {
                padding: 1.25rem 1.5rem 1.5rem;
            }

            .vm-split {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 1.25rem;
            }

            .vm-block {
                background: #fafbfc;
                border-radius: 10px;
                padding: 1rem 1.25rem;
            }

            .vm-block h4 {
                font-size: 0.8rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                color: #94a3b8;
                margin: 0 0 0.5rem;
            }

            .vm-block p {
                color: #475569;
                line-height: 1.65;
                margin: 0;
                font-size: 0.85rem;
            }

            .vm-block ul {
                list-style: none;
                padding: 0;
                margin: 0;
            }

            .vm-block ul li {
                position: relative;
                padding-left: 1.15rem;
                margin-bottom: 0.4rem;
                font-size: 0.85rem;
                color: #475569;
                line-height: 1.55;
            }

            .vm-block ul li:last-child { margin-bottom: 0; }

            .vm-block ul li::before {
                content: '';
                position: absolute;
                left: 0;
                top: 0.55em;
                width: 5px;
                height: 5px;
                border-radius: 50%;
                background: #FEC200;
            }

            @media (max-width: 768px) {
                .vm-split { grid-template-columns: 1fr; }
                .vm-card-head .vm-badge { width: 1.75rem; height: 1.75rem; font-size: 0.7rem; }
                .vm-card-head { padding: 0.75rem 1rem; }
                .vm-card-head h3 { font-size: 0.85rem; }
                .vm-card-body { padding: 1rem 1rem 1.25rem; }
                .vm-block { padding: 0.75rem 1rem; }
            }

            .prodi-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                gap: 1rem;
            }

            .prodi-card {
                background: #fff;
                padding: 1.5rem;
                transition: all 0.15s;
            }

            .prodi-card:hover {
                background: #fafbfc;
            }

            .prodi-card h3 {
                font-size: 1rem;
                font-weight: 600;
                margin-bottom: 0.15rem;
                color: #0f172a;
            }

            .prodi-card .jenjang {
                display: inline-block;
                font-size: 0.75rem;
                font-weight: 600;
                background: #FFF3C4;
                color: #A16207;
                padding: 0.1rem 0.5rem;
                border-radius: 4px;
                margin-bottom: 0.5rem;
            }

            .prodi-card p {
                color: #64748b;
                font-size: 0.85rem;
                line-height: 1.6;
            }

            .landing-footer {
                background: #fff;
                border-top: 1px solid #f1f5f9;
                color: #94a3b8;
                text-align: center;
                padding: 1.5rem;
                font-size: 0.8rem;
            }

            @media (max-width: 768px) {
                .landing-nav { padding: 0 1rem; }
                .landing-nav .nav-links a { font-size: 0.78rem; padding: 0.35rem 0.55rem; }
                .landing-hero { padding: 4rem 1.25rem 2rem; }
                .landing-hero .hero-inner { flex-direction: column; gap: 1.5rem; }
                .landing-hero .hero-text h1 { font-size: 2rem; }
                .landing-hero .hero-text p { font-size: 0.9rem; }
                .landing-hero .hero-image .hero-img { max-width: 180px; }
                .landing-section { padding: 2.5rem 1.25rem; }
                .landing-section h2 { font-size: 1.35rem; }
                .prodi-grid { grid-template-columns: 1fr; }
                .visi-misi-box { padding: 1.25rem; }
            }
        </style>
    </head>
    <body>
        <nav class="landing-nav" id="landingNav">
            <div class="logo">
                <img src="{{ asset('images/eduva/eduva.png') }}" alt="Eduva">
            </div>
            <div class="nav-links">
                <a href="#beranda">Beranda</a>
                <a href="#profil-kampus">Profil Kampus</a>
                <a href="#program-studi">Program Studi</a>
                <a href="#visi-misi">Visi Misi Prodi</a>
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="nav-login-outline">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="nav-login">Masuk</a>
                    @endauth
                @endif
            </div>
        </nav>

        <section id="beranda" class="landing-hero">
            <div class="hero-inner">
                <div class="hero-text">
                    <h1>Selamat Datang di <span class="highlight">Eduva</span></h1>
                    <p>Sistem Informasi Kurikulum &amp; Perkuliahan Digital. Transformasi pengelolaan akademik yang lebih terintegrasi, efektif, dan berorientasi pada hasil. Platform digital terpadu untuk mengelola kurikulum, RPS, dan proses perkuliahan berbasis Outcome-Based Education (OBE) dalam satu sistem yang mudah, terstruktur, dan efisien. Mendukung pembelajaran berkualitas untuk mewujudkan pendidikan yang berorientasi pada capaian dan kebutuhan masa depan.</p>
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}" class="btn">Masuk ke Sistem</a>
                    @endif
                </div>
                <div class="hero-image">
                    <div class="float-wrap">
                        <img class="hero-img" src="{{ asset('images/eduva/eduva-logo.png') }}" alt="Eduva">
                    </div>
                </div>
            </div>
        </section>

        <section id="profil-kampus" class="landing-section landing-section-alt">
            <h2>Profil Kampus</h2>
            <p class="section-subtitle">Politeknik Sawunggalih Aji</p>

            <div class="kampus-card">
                <div class="kampus-img">
                    <img src="{{ asset('images/polsa.png') }}" alt="Gedung Kampus">
                </div>
                <div class="kampus-body">
                    <div class="vm-split">
                        <div class="vm-block">
                            <h4>Visi</h4>
                            <p>Menjadi politeknik unggulan yang menghasilkan sumber daya manusia profesional, kompeten, dan berdaya saing global di bidang bisnis dan teknologi pada tahun 2030.</p>
                        </div>
                        <div class="vm-block">
                            <h4>Misi</h4>
                            <ul>
                                <li>Menyelenggarakan pendidikan vokasi yang berkualitas dan relevan dengan kebutuhan industri.</li>
                                <li>Melaksanakan penelitian terapan yang inovatif dan bermanfaat bagi masyarakat.</li>
                                <li>Menjalin kemitraan strategis dengan dunia usaha, dunia industri, dan dunia kerja.</li>
                                <li>Mengembangkan tata kelola institusi yang profesional, transparan, dan akuntabel.</li>
                                <li>Membudayakan nilai-nilai Pancasila dan kearifan lokal dalam setiap aktivitas tridharma.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="program-studi" class="landing-section">
            <h2>Program Studi</h2>
            <p class="section-subtitle">Pilih program studi sesuai minat dan bakat Anda</p>

            <div class="prodi-grid">
                <div class="prodi-card">
                    <h3>Teknik Informatika</h3>
                    <span class="jenjang">D3</span>
                    <p>Program studi yang mempersiapkan tenaga ahli di bidang pengembangan perangkat lunak, jaringan komputer, dan teknologi informasi. Lulusan mampu merancang, membangun, dan memelihara sistem informasi serta infrastruktur TI untuk mendukung kebutuhan industri dan bisnis.</p>
                </div>

                <div class="prodi-card">
                    <h3>Administrasi Bisnis</h3>
                    <span class="jenjang">D3</span>
                    <p>Program studi yang menghasilkan tenaga profesional di bidang administrasi dan manajemen perkantoran. Lulusan memiliki kompetensi dalam pengelolaan dokumen bisnis, komunikasi perkantoran, manajemen acara, serta administrasi keuangan dan sumber daya manusia.</p>
                </div>

                <div class="prodi-card">
                    <h3>Bisnis Digital</h3>
                    <span class="jenjang">D4</span>
                    <p>Program studi sarjana terapan yang mencetak wirausaha digital dan profesional bisnis berbasis teknologi. Lulusan mampu mengelola platform e-commerce, digital marketing, analitik bisnis, serta mengembangkan strategi transformasi digital untuk organisasi.</p>
                </div>

                <div class="prodi-card">
                    <h3>Teknik Rekayasa Perangkat Lunak</h3>
                    <span class="jenjang">D4</span>
                    <p>Program studi sarjana terapan yang fokus pada rekayasa perangkat lunak skala industri. Lulusan memiliki kemampuan analisis, desain, pengembangan, pengujian, dan pemeliharaan perangkat lunak dengan menerapkan metodologi terkini serta standar kualitas internasional.</p>
                </div>

                <div class="prodi-card">
                    <h3>Akuntansi</h3>
                    <span class="jenjang">D3</span>
                    <p>Program studi yang menyiapkan tenaga ahli akuntansi yang mampu menyusun laporan keuangan, melakukan audit, perpajakan, dan pengendalian internal. Lulusan dibekali kompetensi teknis akuntansi berbasis standar akuntansi keuangan yang berlaku di Indonesia.</p>
                </div>
            </div>
        </section>

        <section id="visi-misi" class="landing-section landing-section-alt">
            <h2>Visi Misi Program Studi</h2>
            <p class="section-subtitle">Arah dan tujuan setiap program studi</p>

            <div class="vm-grid">

                <div class="vm-card">
                    <div class="vm-card-head">
                        <div class="vm-badge">TI</div>
                        <h3>D3 Teknik Informatika</h3>
                    </div>
                    <div class="vm-card-body">
                        <div class="vm-split">
                            <div class="vm-block">
                                <h4>Visi</h4>
                                <p>Menjadi program studi vokasi terdepan di bidang teknologi informasi yang menghasilkan lulusan kompeten, inovatif, dan berakhlak mulia pada tahun 2030.</p>
                            </div>
                            <div class="vm-block">
                                <h4>Misi</h4>
                                <ul>
                                    <li>Menyelenggarakan pendidikan vokasi teknik informatika yang sesuai dengan standar industri.</li>
                                    <li>Mengembangkan pusat inovasi teknologi informasi berbasis riset terapan.</li>
                                    <li>Menjalin kerjasama dengan industri untuk pengembangan kurikulum dan penempatan kerja lulusan.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="vm-card">
                    <div class="vm-card-head">
                        <div class="vm-badge">AB</div>
                        <h3>D3 Administrasi Bisnis</h3>
                    </div>
                    <div class="vm-card-body">
                        <div class="vm-split">
                            <div class="vm-block">
                                <h4>Visi</h4>
                                <p>Menjadi program studi vokasi unggulan di bidang administrasi bisnis yang profesional, berintegritas, dan berdaya saing global pada tahun 2030.</p>
                            </div>
                            <div class="vm-block">
                                <h4>Misi</h4>
                                <ul>
                                    <li>Menyelenggarakan pendidikan vokasi administrasi bisnis yang berkualitas dan relevan.</li>
                                    <li>Mengembangkan kompetensi administrasi perkantoran berbasis teknologi digital.</li>
                                    <li>Membangun kemitraan dengan dunia usaha untuk meningkatkan peluang karir lulusan.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="vm-card">
                    <div class="vm-card-head">
                        <div class="vm-badge">BD</div>
                        <h3>D4 Bisnis Digital</h3>
                    </div>
                    <div class="vm-card-body">
                        <div class="vm-split">
                            <div class="vm-block">
                                <h4>Visi</h4>
                                <p>Menjadi program studi sarjana terapan bisnis digital yang inovatif, kreatif, dan berdaya saing internasional pada tahun 2030.</p>
                            </div>
                            <div class="vm-block">
                                <h4>Misi</h4>
                                <ul>
                                    <li>Menyelenggarakan pendidikan bisnis digital yang mengintegrasikan teknologi dan kewirausahaan.</li>
                                    <li>Mengembangkan riset terapan di bidang transformasi digital dan e-commerce.</li>
                                    <li>Menciptakan wirausaha digital yang mampu bersaing di pasar global.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="vm-card">
                    <div class="vm-card-head">
                        <div class="vm-badge">RPL</div>
                        <h3>D4 Teknik Rekayasa Perangkat Lunak</h3>
                    </div>
                    <div class="vm-card-body">
                        <div class="vm-split">
                            <div class="vm-block">
                                <h4>Visi</h4>
                                <p>Menjadi program studi sarjana terapan rekayasa perangkat lunak yang unggul dalam inovasi dan berstandar internasional pada tahun 2030.</p>
                            </div>
                            <div class="vm-block">
                                <h4>Misi</h4>
                                <ul>
                                    <li>Menyelenggarakan pendidikan rekayasa perangkat lunak yang berorientasi pada kebutuhan industri 4.0.</li>
                                    <li>Mengembangkan pusat pengembangan perangkat lunak berbasis riset terapan dan open source.</li>
                                    <li>Menjalin kerjasama dengan industri teknologi untuk sertifikasi kompetensi dan penyerapan lulusan.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="vm-card">
                    <div class="vm-card-head">
                        <div class="vm-badge">AK</div>
                        <h3>D3 Akuntansi</h3>
                    </div>
                    <div class="vm-card-body">
                        <div class="vm-split">
                            <div class="vm-block">
                                <h4>Visi</h4>
                                <p>Menjadi program studi vokasi akuntansi yang terpercaya, profesional, dan berdaya saing nasional pada tahun 2030.</p>
                            </div>
                            <div class="vm-block">
                                <h4>Misi</h4>
                                <ul>
                                    <li>Menyelenggarakan pendidikan vokasi akuntansi berbasis praktik dan standar profesi.</li>
                                    <li>Mengembangkan kompetensi akuntansi digital dan sistem informasi akuntansi terpadu.</li>
                                    <li>Membangun jejaring dengan profesi akuntansi dan industri untuk meningkatkan kualitas lulusan.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        <footer class="landing-footer">
            &copy; {{ date('Y') }} EDUVA Polsa — Politeknik Sawunggalih Aji. All rights reserved.
        </footer>

        <script>
        const nav = document.getElementById('landingNav');
        window.addEventListener('scroll', () => {
            nav.classList.toggle('scrolled', window.scrollY > 20);
        });

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.landing-section').forEach(el => observer.observe(el));
        </script>
    </body>
</html>
