@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-receipt"></i> Detail Pesanan #{{ $order->id }}</h4>
                    <span class="badge fs-6
                        @if($order->status == 'pending') bg-warning text-dark
                        @elseif($order->status == 'diproses') bg-info
                        @elseif($order->status == 'selesai') bg-success
                        @else bg-danger @endif">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="row">
                        <!-- Informasi Pengiriman -->
                        <div class="col-md-6">
                            <h5 class="text-primary mb-3"><i class="fas fa-truck"></i> Informasi Pengiriman</h5>
                            <div class="mb-2">
                                <strong>Nama:</strong> {{ $order->customer_name }}
                            </div>
                            <div class="mb-2">
                                <strong>Telepon:</strong> {{ $order->customer_phone }}
                            </div>
                            <div class="mb-2">
                                <strong>Alamat:</strong> {{ $order->customer_address }}
                            </div>
                            @if($order->notes)
                                <div class="mb-2">
                                    <strong>Catatan:</strong> {{ $order->notes }}
                                </div>
                            @endif
                        </div>

                        <!-- Informasi Pembayaran -->
                        <div class="col-md-6">
                            <h5 class="text-primary mb-3"><i class="fas fa-credit-card"></i> Informasi Pembayaran</h5>
                            <div class="mb-2">
                                <strong>Metode Pembayaran:</strong>
                                @if($order->payment_method == 'tunai')
                                    <span class="badge bg-success fs-6">
                                        <i class="fas fa-money-bill-wave"></i> Tunai
                                    </span>
                                @else
                                    <span class="badge bg-primary fs-6">
                                        <i class="fas fa-mobile-alt"></i> E-Wallet
                                    </span>
                                @endif
                            </div>
                            @if($order->payment_method == 'e-wallet' && $order->e_wallet_type)
                                <div class="mb-2">
                                    <strong>E-Wallet:</strong>
                                    @if($order->e_wallet_type == 'dana')
                                        <img src="https://upload.wikimedia.org/wikipedia/commons/7/72/Logo_dana_blue.svg" alt="DANA" style="height: 24px; margin-right: 8px;"> DANA
                                    @elseif($order->e_wallet_type == 'gopay')
                                        <img src="https://upload.wikimedia.org/wikipedia/commons/8/86/Gopay_logo.svg" alt="GoPay" style="height: 24px; margin-right: 8px;"> GoPay
                                    @elseif($order->e_wallet_type == 'shopeepay')
                                        <img src="https://upload.wikimedia.org/wikipedia/commons/8/8d/ShopeePay_logo.svg" alt="ShopeePay" style="height: 24px; margin-right: 8px;"> ShopeePay
                                    @endif
                                </div>
                            @endif
                            <div class="mb-2">
                                <strong>Total Pembayaran:</strong>
                                <span class="text-primary fw-bold fs-5">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                            </div>
                            <div class="mb-2">
                                <strong>Tanggal Pesanan:</strong> {{ $order->created_at->format('d M Y, H:i') }}
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Daftar Produk -->
                    <h5 class="text-primary mb-3"><i class="fas fa-shopping-cart"></i> Produk yang Dipesan</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Harga</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->orderItems as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($item->product && $item->product->image)
                                                    <img src="{{ asset('storage/' . $item->product->image) }}"
                                                         alt="{{ $item->product->name }}"
                                                         class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                                @endif
                                                <div>
                                                    <strong>{{ $item->product ? $item->product->name : 'Produk tidak tersedia' }}</strong>
                                                    @if($item->product)
                                                        <br><small class="text-muted">{{ $item->product->category->name ?? '' }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-end">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                        <td class="text-end fw-bold">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Total:</td>
                                    <td class="text-end fw-bold text-primary fs-5">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Status dan Aksi -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle"></i> Status Pesanan</h6>
                                <p class="mb-0">
                                    @if($order->status == 'pending')
                                        Pesanan Anda sedang menunggu konfirmasi dari admin.
                                    @elseif($order->status == 'diproses')
                                        Pesanan Anda sedang diproses.
                                    @elseif($order->status == 'selesai')
                                        Pesanan Anda telah selesai. Terima kasih telah berbelanja!
                                    @else
                                        Pesanan dibatalkan.
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="{{ route('customer.orders.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali ke Daftar Pesanan
                            </a>
                            @if($order->status == 'selesai')
                                <a href="{{ route('home') }}" class="btn btn-primary">
                                    <i class="fas fa-shopping-bag"></i> Belanja Lagi
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
