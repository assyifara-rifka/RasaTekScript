@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4"><i class="fas fa-credit-card"></i> Checkout</h2>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Informasi Pengiriman</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('orders.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap *</label>
                            <input type="text" name="customer_name" class="form-control" 
                                   value="{{ auth()->user()->name }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">No. Telepon *</label>
                            <input type="tel" name="customer_phone" class="form-control" 
                                   value="{{ auth()->user()->phone }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alamat Lengkap *</label>
                            <textarea name="customer_address" class="form-control" rows="3" required>{{ auth()->user()->address }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Metode Pembayaran *</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="tunai" value="tunai" required>
                                        <label class="form-check-label" for="tunai">
                                            <i class="fas fa-money-bill-wave"></i> Tunai
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="e-wallet" value="e-wallet" required>
                                        <label class="form-check-label" for="e-wallet">
                                            <i class="fas fa-mobile-alt"></i> E-Wallet
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3" id="e-wallet-options" style="display: none;">
                            <label class="form-label">Pilih E-Wallet *</label>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="e_wallet_type" id="dana" value="dana">
                                        <label class="form-check-label" for="dana">
                                            <img src="https://upload.wikimedia.org/wikipedia/commons/7/72/Logo_dana_blue.svg" alt="DANA" style="height: 20px; margin-right: 5px;"> DANA
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="e_wallet_type" id="gopay" value="gopay">
                                        <label class="form-check-label" for="gopay">
                                            <img src="https://upload.wikimedia.org/wikipedia/commons/8/86/Gopay_logo.svg" alt="GoPay" style="height: 20px; margin-right: 5px;"> GoPay
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="e_wallet_type" id="shopeepay" value="shopeepay">
                                        <label class="form-check-label" for="shopeepay">
                                            <img src="https://upload.wikimedia.org/wikipedia/commons/8/8d/ShopeePay_logo.svg" alt="ShopeePay" style="height: 20px; margin-right: 5px;"> ShopeePay
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Catatan (Opsional)</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Tambahkan catatan untuk pesanan Anda..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-check-circle"></i> Buat Pesanan
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Ringkasan Pesanan</h5>
                </div>
                <div class="card-body">
                    @foreach($cart as $item)
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ $item['name'] }} ({{ $item['quantity'] }}x)</span>
                            <span>Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                    <hr>
                    <div class="d-flex justify-content-between">
                        <strong>Total:</strong>
                        <strong class="text-primary fs-5">Rp {{ number_format($total, 0, ',', '.') }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Handle payment method change
    $('input[name="payment_method"]').change(function() {
        if ($(this).val() === 'e-wallet') {
            $('#e-wallet-options').show();
            // Make e-wallet type required when e-wallet is selected
            $('input[name="e_wallet_type"]').attr('required', true);
        } else {
            $('#e-wallet-options').hide();
            // Remove required attribute when tunai is selected
            $('input[name="e_wallet_type"]').attr('required', false);
            // Uncheck all e-wallet options
            $('input[name="e_wallet_type"]').prop('checked', false);
        }
    });
});
</script>
@endpush
