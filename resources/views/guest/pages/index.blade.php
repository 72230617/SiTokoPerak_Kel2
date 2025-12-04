@extends('guest.layouts.main')
@section('title', 'Index')

@section('content')

    {{-- ==================== HERO ==================== --}}
    <div class="main-banner" id="top">
        <div class="banner-background">
            <img src="{{ asset('assets/images/malioboro2.jpg') }}" alt="Keraton Yogyakarta"
                style="width: 100%; height: 100%; object-fit: cover; position: absolute; top: 0; left: 0; z-index: -1;">
        </div>

        <div class="banner-content">
            <h1>Perak Asli Kotagede – Warisan Seni dari Jogja</h1>
            <p>
                Karya seni perak dari Kotagede, Yogyakarta yang menggabungkan tradisi, ketelitian, dan keanggunan.
                Setiap detail menyimpan cerita, setiap ukiran merekam sejarah.
            </p>
            <a href="javascript:void(0);" class="btn btn-danger btn-lg mt-3 scroll-to-produk">Beli Sekarang</a>
        </div>
    </div>

    {{-- ==================== KATEGORI ==================== --}}
    <section class="categories">
        <div class="container">
            <div class="section-heading">
                <h2>Kategori Produk</h2>
                <p class="text-muted">Temukan berbagai koleksi produk perak terbaik kami</p>
            </div>

            <div id="categoryCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    @foreach ($kategoris->chunk(3) as $key => $chunk)
                        <div class="carousel-item {{ $key === 0 ? 'active' : '' }}">
                            <div class="row">
                                @foreach ($chunk as $kategori)
                                    <div class="col-lg-4 col-md-6 mb-4">
                                        <a
                                            href="{{ route('guest-katalog', array_merge(request()->except('page'), ['kategori' => $kategori->slug])) }}">
                                            <div class="card category-card h-100">
                                                <img src="{{ asset('assets/images/' . $kategori->slug . '.jpg') }}"
                                                    alt="{{ $kategori->nama_kategori_produk }}"
                                                    class="card-img-top category-img"
                                                    onerror="this.onerror=null;this.src='{{ asset('assets/images/kategori-default.jpg') }}';">
                                                <div class="card-body">
                                                    <h5 class="card-title">{{ $kategori->nama_kategori_produk }}</h5>
                                                    <p class="text-muted subtitle">Pesona Perak</p>
                                                    <span class="btn btn-outline-dark btn-sm">Lihat Produk</span>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <button class="carousel-control-prev" type="button" data-bs-target="#categoryCarousel"
                    data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#categoryCarousel"
                    data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden"></span>
                </button>
            </div>
        </div>
    </section>

    {{-- ==================== PRODUK TERBARU ==================== --}}
    <section class="products">
        <div class="container">
            <div class="section-heading">
                <h2>Produk Terbaru Kami!</h2>
                <span>Temukan Produk Terfavoritmu!</span>
            </div>
        </div>

        <div class="container">
            <div class="row">
                @forelse ($randomProduks as $produk)
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="product-item">
                            <div class="thumb">
                                <a href="{{ route('guest-singleProduct', $produk->slug) }}" class="img-link">
                                    <img src="{{ asset('storage/' . (optional($produk->fotoProduk->first())->file_foto_produk ?? 'placeholder.jpg')) }}"
                                        alt="{{ $produk->nama_produk }}"
                                        onerror="this.onerror=null;this.src='{{ asset('images/produk-default.jpg') }}';">
                                </a>

                                <div class="hover-content">
                                    <ul>
                                        {{-- SHOW / VIEW --}}
                                        <li>
                                            <a href="{{ route('guest-singleProduct', $produk->slug) }}" class="btn-show"
                                                data-id="{{ $produk->id }}">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                        </li>

                                        {{-- LIKE --}}
                                        <li>
                                            <button type="button" class="like-btn" data-id="{{ $produk->id }}">
                                                <i
                                                    class="fa fa-star star-icon {{ ($produk->likes_count ?? 0) > 0 ? 'active' : '' }}"></i>
                                            </button>
                                        </li>

                                        {{-- CART (kalau mau dipakai nanti) --}}
                                        <li>
                                            <button type="button" class="add-cart-btn" data-id="{{ $produk->id }}">
                                                <i class="fa fa-shopping-cart"></i>
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="down-content">
                                <h4>{{ $produk->nama_produk }}</h4>
                                <span class="product-price">
                                    Rp {{ number_format($produk->harga, 0, ',', '.') }}
                                </span>
                                <ul class="stars">
                                    @for ($i = 0; $i < 5; $i++)
                                        <li><i class="fa fa-star"></i></li>
                                    @endfor
                                </ul>
                                <p class="product-reviews">
                                    {{ $produk->views_count ?? 0 }}x dilihat • {{ $produk->likes_count ?? 0 }} suka
                                </p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center">
                        <p>Produk belum tersedia saat ini.</p>
                    </div>
                @endforelse
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="text-center mt-5">
                        <a href="{{ route('guest-katalog') }}" class="see-all-button btn">Lihat Semua</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ==================== ABOUT SHORT ==================== --}}
    <section class="about-us">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7 col-md-12">
                    <div class="about-image">
                        <img src="{{ asset('assets/images/kerajinan-perak-kota-ged.png') }}"
                            alt="Sentra Kerajinan Perak Kotagede" class="img-fluid">
                    </div>
                </div>
                <div class="col-lg-5 col-md-12">
                    <div class="about-content">
                        <h3>TekoPerakku</h3>
                        <p>
                            TekoPerakku menghadirkan kerajinan perak asli Kotagede dengan kualitas terbaik.
                            Setiap karya diproses secara teliti oleh pengrajin berpengalaman untuk menjaga keaslian dan
                            keindahan tradisi.
                        </p>
                        <a href="{{ route('guest-about') }}" class="btn btn-primary about-btn">Pelajari Lebih Lanjut</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ==================== CSS KHUSUS HOVER ==================== --}}
    <style>
        .product-item .thumb,
        .item .thumb {
            position: relative;
            overflow: hidden;
            border-radius: 6px;
        }

        .product-item .hover-content,
        .item .hover-content {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 5;
            opacity: 0;
            visibility: hidden;
            transition: opacity .2s ease, visibility .2s ease;
            pointer-events: auto;
        }

        .product-item:hover .hover-content,
        .item:hover .hover-content {
            opacity: 1;
            visibility: visible;
        }

        .product-item .hover-content ul,
        .item .hover-content ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .product-item .hover-content ul li,
        .item .hover-content ul li {
            display: inline-block;
            margin-left: 5px;
        }

        .product-item .hover-content a,
        .product-item .hover-content button,
        .item .hover-content a,
        .item .hover-content button {
            background: rgba(0, 0, 0, 0.6);
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            color: #fff;
        }

        .star-icon {
            transition: .2s ease;
        }

        .star-icon.active {
            color: #ffc107 !important;
        }
    </style>

@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Scroll ke produk
            const scrollBtn = document.querySelector('.scroll-to-produk');
            if (scrollBtn) {
                scrollBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    document.querySelector('.products')?.scrollIntoView({
                        behavior: 'smooth'
                    });
                });
            }

            // LIKE
            document.querySelectorAll('.like-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const productId = this.dataset.id;
                    const icon = this.querySelector('.star-icon');
                    const infoText = this.closest('.product-item')
                        ?.querySelector('.product-reviews');

                    fetch(`/produk/${productId}/like`, {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({})
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.liked) {
                                icon.classList.add('active');
                            } else {
                                icon.classList.remove('active');
                            }

                            if (infoText && typeof data.totalLikes !== 'undefined') {
                                const parts = infoText.innerText.split('•');
                                const viewsPart = parts[0] ?? '0x dilihat ';
                                infoText.innerText = viewsPart.trim() + ' • ' + (data
                                    .totalLikes ?? 0) + ' suka';
                            }
                        })
                        .catch(err => console.error(err));
                });
            });

            // VIEW lewat icon mata
            document.querySelectorAll('.btn-show').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();

                    const productId = this.dataset.id;
                    const url = this.getAttribute('href');
                    const infoText = this.closest('.product-item')
                        ?.querySelector('.product-reviews');

                    fetch(`/produk/${productId}/view`, {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({})
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (infoText && typeof data.totalViews !== 'undefined') {
                                const parts = infoText.innerText.split('•');
                                const likesPart = parts[1] ?? '0 suka';
                                infoText.innerText = (data.totalViews ?? 0) + 'x dilihat • ' +
                                    likesPart.trim();
                            }
                        })
                        .catch(err => console.error(err))
                        .finally(() => {
                            window.location.href = url;
                        });
                });
            });
        });
    </script>
@endpush
