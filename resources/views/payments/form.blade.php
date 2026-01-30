@extends('layouts.admin')

@section('title', 'Give with M-Pesa')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Mobile Money Giving</h3>
                </div>
                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('give.process') }}" method="POST" id="giving-form">
                        @csrf
                        
                        <div class="form-group">
                            <label for="amount">Amount (TZS)</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">TSh</span>
                                </div>
                                <input type="number" name="amount" id="amount" class="form-control @error('amount') is-invalid @enderror" 
                                       step="1" min="1" required placeholder="e.g. 10000">
                                @error('amount')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="category">Category</label>
                            <select name="category" id="category" class="form-control @error('category') is-invalid @enderror" required>
                                <option value="">Select Category</option>
                                @forelse($categories as $category)
                                    <option value="{{ $category->name }}">{{ $category->name }}</option>
                                @empty
                                    <option value="General">General</option>
                                @endforelse
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="network">Mobile Network</label>
                            <select name="network" id="network" class="form-control @error('network') is-invalid @enderror" required>
                                <option value="VODACOM">Vodacom M-Pesa</option>
                                <option value="AIRTEL">Airtel Money</option>
                                <option value="TIGO">Tigo Pesa</option>
                                <option value="HALOPESA">Halopesa</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="phone_number">Phone Number</label>
                            <input type="text" name="phone_number" id="phone_number" class="form-control @error('phone_number') is-invalid @enderror" 
                                   required placeholder="e.g. 0756670798">
                            <small class="form-text text-muted">Enter the number you will pay from.</small>
                            @error('phone_number')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description">Note (Optional)</label>
                            <textarea name="description" id="description" class="form-control" rows="3" placeholder="Add a note..."></textarea>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-mobile-alt"></i> You will receive a prompt on your phone to approve the payment.
                        </div>

                        <button type="submit" class="btn btn-success btn-lg btn-block">
                            <i class="fas fa-paper-plane"></i> Send Prompt
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
