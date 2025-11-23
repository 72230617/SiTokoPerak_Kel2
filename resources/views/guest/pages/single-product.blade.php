@extends('guest.layouts.main')

@section('title', $produk->nama_produk)

@push('styles')
    {{-- Memuat file CSS khusus untuk halaman ini --}}
    <link rel="stylesheet" href="{{ asset('assets/css/detail-product.css') }}">
@endpush

@section('content')
    {{-- Breadcrumb Navigation --}}
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <nav aria-label="breadcrumb" class="product-breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('guest-katalog') }}">Katalog</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $produk->nama_produk }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    {{-- Product Detail Section --}}
    <section class="section" id="product">
        <div class="container">
            <div class="row">
                {{-- Kolom Kiri: Galeri Gambar Produk --}}
                <div class="col-lg-6">
                    <div class="gallery-wrapper">
                        {{-- Gambar Utama --}}
                        {{-- Gambar Utama yang Bisa Diklik --}}
                        <div class="main-image-container mb-3">
                            @if($produk->fotoProduk->isNotEmpty())
                                {{-- Bungkus <img> dengan <a> --}}
                                    <a href="{{ asset('storage/' . $produk->fotoProduk->first()->file_foto_produk) }}"
                                        data-lightbox="product-gallery">
                                        <img src="{{ asset('storage/' . $produk->fotoProduk->first()->file_foto_produk) }}"
                                            alt="{{ $produk->nama_produk }}" id="mainProductImage" class="img-fluid">
                                    </a>
                            @else
                                    <a href="{{ asset('assets/images/produk-default.jpg') }}" data-lightbox="product-gallery">
                                        <img src="{{ asset('assets/images/produk-default.jpg') }}" alt="Produk Default"
                                            id="mainProductImage" class="img-fluid">
                                    </a>
                                @endif
                        </div>

                        {{-- Thumbnail Gambar Scroller --}}
                        <div class="thumbnail-scroller-wrapper">
                            <button class="thumb-nav-btn prev" id="thumbPrevBtn"><i class="fa fa-chevron-left"></i></button>
                            <div class="thumbnail-container" id="thumbnailContainer">
                                @foreach ($produk->fotoProduk as $index => $foto)
                                    <div class="thumbnail-item {{ $index == 0 ? 'active' : '' }}">
                                        <img src="{{ asset('storage/' . $foto->file_foto_produk) }}" alt="Thumbnail"
                                            class="img-fluid" onclick="changeMainImage(this)">
                                    </div>
                                @endforeach
                            </div>
                            <button class="thumb-nav-btn next" id="thumbNextBtn"><i
                                    class="fa fa-chevron-right"></i></button>
                        </div>
                    </div>
                </div>

                {{-- Kolom Kanan: Informasi Produk --}}
                <div class="col-lg-6">
                    {{-- GANTI SELURUH ISI DARI <div class="right-content"> DENGAN KODE INI --}}
                        <div class="right-content">

                            {{-- BARIS 1: JUDUL PRODUK DAN IKON AKSI --}}
                            <div class="product-header">
                                <h2 class="product-title">{{ $produk->nama_produk }}</h2>
                                <div class="action-icons">
                                    <button class="btn btn-icon" id="copyLinkBtn" title="Bagikan Tautan">
                                        <i class="fa fa-share-alt"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="rating-stock-wrapper">
                                <div class="rating-wrapper">
                                    <div class="stars-custom">
                                        <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i
                                            class="fa fa-star"></i><i class="fa fa-star"></i>
                                    </div>
                                </div>
                                <span class="stock-status">IN STOCK</span>
                            </div>
                            <span class="price">Rp {{ number_format($produk->harga, 0, ',', '.') }}</span>
                            @php
                                $usaha = $produk->usaha->first();
                            @endphp

                            <div class="usaha-info">
                                @if ($usaha)
                                    <a href="{{ route('guest-detail-usaha', ['usaha' => $usaha, 'from_product' => $produk->slug]) }}"
                                        class="usaha-link">
                                        <img src="{{ asset('assets/images/kategori-default.jpg') }}" alt="Logo Usaha"
                                            class="usaha-avatar">
                                        <div class="usaha-details">
                                            <span class="usaha-name">{{ $usaha->nama_usaha }}</span>
                                            <span
                                                class="usaha-spesialisasi">{{ $usaha->deskripsi_usaha ?? 'Kerajinan Perak Kotagede' }}</span>
                                        </div>
                                    </a>
                                    <div class="social-links">
                                        <a href="#" target="_blank" class="social-icon email" title="Email"><i
                                                class="fa fa-envelope"></i></a>
                                        <a href="https://wa.me/" target="_blank" class="social-icon whatsapp"
                                            title="WhatsApp"><i class="fa fa-phone"></i></a>
                                        <a href="#" target="_blank" class="social-icon instagram" title="Instagram"><i
                                                class="fa fa-instagram"></i></a>
                                        <a href="#" target="_blank" class="social-icon tiktok" title="TikTok"><i
                                                class="fa fa-tiktok"></i></a>
                                        <a href="#" target="_blank" class="social-icon shopee" title="Shopee">
                                            <img src="{{ asset('assets/images/shopee-icon.png') }}" alt="Shopee">
                                        </a>
                                        <a href="#" target="_blank" class="social-icon tokped" title="Tokopedia">
                                            <img src="{{ asset('assets/images/tokopedia-icon.png') }}" alt="Tokopedia">
                                        </a>
                                    </div>
                                @else
                                    <p class="text-muted">Informasi usaha tidak tersedia.</p>
                                @endif
                            </div>

                            {{-- DESKRIPSI DAN DETAIL PRODUK --}}
                            <div class="product-details">
                                <h5>Detail</h5>
                                <p>{{ $produk->deskripsi }}</p>
                            </div>

                            <form action="{{ route('cart.add', $produk->slug) }}" method="POST"
                                onsubmit="this.querySelector('button').disabled = true;">
                                @csrf
                                    <button type="submit">
                                        <a class="see-all-button btn">
                                            <i class="fa fa-shopping-cart me-2"></i> Tambah ke Keranjang
                                        </a>
                                    </button>
                            </form>


                        </div>
                    </div>
                </div>
            </div>
    </section>

    <section class="products">
        <div class="container">
            <div class="section-heading">
                <h2>Produk Terkait</h2>
            </div>
        </div>
        <div class="container">
            <div class="row">
                @foreach ($randomProduks as $relatedProduk)
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="product-item">
                            <a href="{{ route('guest-singleProduct', $relatedProduk->slug) }}">
                                <div class="thumb">
                                    <img src="{{ asset('storage/' . optional($relatedProduk->fotoProduk->first())->file_foto_produk) }}"
                                        alt="{{ $relatedProduk->nama_produk }}"
                                        onerror="this.onerror=null;this.src='{{ asset('images/produk-default.jpg') }}';">
                                </div>
                                <div class="down-content">
                                    <h4>{{ $relatedProduk->nama_produk }}</h4>
                                    <span class="product-price">Rp {{ number_format($relatedProduk->harga, 0, ',', '.') }}</span>
                                    <ul class="stars">
                                        @for ($i = 0; $i < 5; $i++)
                                            <li><i class="fa fa-star"></i></li>
                                        @endfor
                                    </ul>
                                    <p class="product-reviews">20 Reviews</p>
                                </div>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="col-lg-12">
                <div class="text-center mt-5">
                    <a href="{{ route('guest-katalog') }}" class="see-all-button btn">Lihat Semua</a>
                </div>
            </div>
        </div>
    </section>

    {{-- Reviews and Rating Section --}}
    <section class="section" id="reviews">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-heading">
                        <h2>Ulasan dan Rating</h2>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="review-summary-container">
                        <div class="rating-summary">
                            <div class="average-rating">
                                <span class="rating-value">{{ $produk->average_rating }}</span>
                                <span class="rating-max"> dari 5</span>
                            </div>
                            <div class="stars-display">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= $produk->average_rating)
                                        <i class="fa fa-star"></i>
                                    @else
                                        <i class="fa fa-star-o"></i>
                                    @endif
                                @endfor
                            </div>
                        </div>
                        <div class="filter-buttons">
                            <button class="filter-btn active" data-filter-type="all">Semua</button>
                            <button class="filter-btn" data-filter-type="rating" data-filter-value="5">5 Bintang ({{ $produk->rating_counts[5] ?? 0 }})</button>
                            <button class="filter-btn" data-filter-type="rating" data-filter-value="4">4 Bintang ({{ $produk->rating_counts[4] ?? 0 }})</button>
                            <button class="filter-btn" data-filter-type="rating" data-filter-value="3">3 Bintang ({{ $produk->rating_counts[3] ?? 0 }})</button>
                            <button class="filter-btn" data-filter-type="rating" data-filter-value="2">2 Bintang ({{ $produk->rating_counts[2] ?? 0 }})</button>
                            <button class="filter-btn" data-filter-type="rating" data-filter-value="1">1 Bintang ({{ $produk->rating_counts[1] ?? 0 }})</button>
                            <button class="filter-btn" data-filter-type="comment" data-filter-value="true">Dengan Komentar ({{ $produk->reviews_with_comment_count }})</button>
                            <button class="filter-btn" data-filter-type="media" data-filter-value="true">Dengan Media ({{ $produk->reviews_with_media_count }})</button>
                        </div>
                    </div>
                    <div class="reviews-wrapper">
                        @forelse ($reviews as $review)
                            <div class="review-item" data-rating="{{ $review->rating }}" data-comment="{{ $review->komentar ? 'true' : 'false' }}" data-media="{{ $review->media->isNotEmpty() ? 'true' : 'false' }}">
                                <div class="reviewer-info">
                                    <span class="reviewer-name">{{ $review->user->name }}</span>
                                    <span class="review-date">{{ $review->created_at->format('d M Y') }}</span>
                                </div>
                                <div class="review-rating">
                                    @for ($i = 0; $i < $review->rating; $i++)
                                        <i class="fa fa-star"></i>
                                    @endfor
                                    @for ($i = $review->rating; $i < 5; $i++)
                                        <i class="fa fa-star-o"></i>
                                    @endfor
                                </div>
                                <div class="review-comment">
                                    <p>{{ $review->komentar }}</p>
                                </div>
                            </div>
                        @empty
                            <p>Belum ada ulasan untuk produk ini.</p>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    @auth
                        <button type="button" class="btn see-all-button" data-bs-toggle="modal" data-bs-target="#reviewModal">
                            Tulis Ulasan Anda
                        </button>
                    @else
                        <p>Silahkan masuk untuk menulis ulasan <br><a href="{{ route('login') }}" class="see-all-button btn">Masuk</a></p>
                    @endauth
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        // Fungsi untuk mengubah gambar utama, sekarang bisa diakses secara global
        function changeMainImage(thumbnailElement) {
            const index = Array.from(document.querySelectorAll('.thumbnail-item')).indexOf(thumbnailElement.parentElement);
            if (index > -1) {
                updateGallery(index);
            }
        }

        // Fungsi utama untuk mengupdate galeri
        function updateGallery(index) {
            const thumbnails = document.querySelectorAll('.thumbnail-item');
            const mainImage = document.getElementById('mainProductImage');
            const mainImageLink = mainImage ? mainImage.parentElement : null;

            if (thumbnails.length === 0 || !mainImage) return;

            const newImageSrc = thumbnails[index].querySelector('img').src;

            mainImage.src = newImageSrc;
            if (mainImageLink) {
                mainImageLink.href = newImageSrc;
            }

            thumbnails.forEach(thumb => thumb.classList.remove('active'));
            thumbnails[index].classList.add('active');

            // Simpan index saat ini untuk tombol prev/next
            window.currentImageIndex = index;
        }

        // Jalankan semua event listener setelah halaman selesai dimuat
        document.addEventListener('DOMContentLoaded', function () {
            window.currentImageIndex = 0; // Inisialisasi index gambar

            // --- Logika untuk Tombol Navigasi Thumbnail ---
            const thumbContainer = document.getElementById('thumbnailContainer');
            const thumbPrevBtn = document.getElementById('thumbPrevBtn');
            const thumbNextBtn = document.getElementById('thumbNextBtn');

            if (thumbContainer) {
                const scrollAmount = 300;
                thumbNextBtn.addEventListener('click', () => {
                    let nextIndex = window.currentImageIndex + 1;
                    if (nextIndex >= thumbContainer.children.length) {
                        nextIndex = 0;
                    }
                    updateGallery(nextIndex);
                    // Scroll to the active thumbnail
                    thumbContainer.children[nextIndex].scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                });
                thumbPrevBtn.addEventListener('click', () => {
                    let prevIndex = window.currentImageIndex - 1;
                    if (prevIndex < 0) {
                        prevIndex = thumbContainer.children.length - 1;
                    }
                    updateGallery(prevIndex);
                    // Scroll to the active thumbnail
                    thumbContainer.children[prevIndex].scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                });
            }

            // --- Logika untuk Tombol Copy Link ---
            const copyBtn = document.getElementById('copyLinkBtn');
            if (copyBtn) {
                copyBtn.addEventListener('click', function () {
                    const urlToCopy = window.location.href;
                    navigator.clipboard.writeText(urlToCopy).then(() => {
                        const icon = copyBtn.querySelector('i');
                        const originalIconClass = icon.className;
                        icon.className = 'fa fa-check';
                        copyBtn.disabled = true;
                        setTimeout(() => {
                            icon.className = originalIconClass;
                            copyBtn.disabled = false;
                        }, 2000);
                    }).catch(err => console.error('Gagal menyalin:', err));
                });
            }

            // Review Filter Logic
            const filterButtons = document.querySelectorAll('.filter-btn');
            const reviewItems = document.querySelectorAll('.review-item');

            filterButtons.forEach(button => {
                button.addEventListener('click', function () {
                    // Set active state
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');

                    const filterType = this.dataset.filterType;
                    const filterValue = this.dataset.filterValue;

                    reviewItems.forEach(item => {
                        if (filterType === 'all') {
                            item.style.display = 'block';
                        } else {
                            const itemValue = item.dataset[filterType];
                            if (itemValue === filterValue) {
                                item.style.display = 'block';
                            } else {
                                item.style.display = 'none';
                            }
                        }
                    });
                });
            });
        });
    </script>
@endpush
