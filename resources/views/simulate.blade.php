@extends('layouts.app')
@section('title', __('menu.simulate'))

@section('content')
@php $locale = app()->getLocale(); @endphp

{{-- Page hero --}}
<div class="page-hero">
    <div class="container">
        <div class="page-hero__content">
            <h1 class="page-hero__title">@lang('menu.simulate')</h1>
            <ul class="page-hero__breadcrumb">
                <li><a href="{{ route('home', ['locale' => $locale]) }}">@lang('menu.home')</a></li>
                <li class="sep"><i class="fas fa-chevron-right"></i></li>
                <li>@lang('menu.simulate')</li>
            </ul>
        </div>
    </div>
</div>

<section class="py-24 bg-white">
    <div class="container">

        {{-- Simulation form --}}
        <div class="row justify-content-center mb-12">
            <div class="col-lg-7 wow fadeInUp" data-wow-duration="900ms">
                <div class="form-card">
                    <div class="section-label mb-2">@lang('menu.simulate')</div>
                    <h2 class="section-title mb-6">@lang('home.simulate.sectitle')</h2>
                    <form method="POST" action="{{ route('loan.simulate') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('simulate.label_amount')</label>
                                    <input type="number" name="amount" class="form-control" min="1" step="0.01"
                                           value="{{ isset($loan) ? $loan->amount : '' }}"
                                           placeholder="@lang('simulate.placeholder_amount')" required>
                                    @error('amount')<span class="form-error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('simulate.label_duree')</label>
                                    <input type="number" name="duration" class="form-control" min="1"
                                           value="{{ isset($loan) ? $loan->duration : '' }}"
                                           placeholder="@lang('simulate.placeholder_duree')" required>
                                    @error('duration')<span class="form-error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('simulate.label_taux')</label>
                                    <input type="number" name="interest_rate" class="form-control" min="0" step="0.01"
                                           value="{{ isset($loan) ? $loan->interest_rate : '' }}"
                                           placeholder="@lang('simulate.placeholder_taux')" required>
                                    @error('interest_rate')<span class="form-error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-12 mt-2">
                                <button type="submit" class="btn-primary btn-primary--lg w-100 justify-content-center">
                                    <i class="fas fa-calculator"></i>
                                    @lang('simulate.button')
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Results --}}
        @isset($monthlyPayment)
        <div class="wow fadeInUp" data-wow-duration="900ms">
            <div class="simulate-summary">
                <div class="simulate-summary__card">
                    <span>@lang('simulate.paiement')</span>
                    <strong>{{ number_format($monthlyPayment, 2, ',', ' ') }} €</strong>
                </div>
                <div class="simulate-summary__card">
                    <span>@lang('simulate.terms')</span>
                    <strong>{{ $loan->duration }} @lang('simulate.table_month')s</strong>
                </div>
                <div class="simulate-summary__card">
                    <span>@lang('simulate.total')</span>
                    <strong>{{ number_format($monthlyPayment * $loan->duration, 2, ',', ' ') }} €</strong>
                </div>
                <div class="simulate-summary__card">
                    <span>@lang('simulate.label_taux')</span>
                    <strong>{{ $loan->interest_rate }} %</strong>
                </div>
            </div>

            <h3 style="font-family:'Fraunces',serif;color:var(--navy);font-size:1.25rem;font-weight:700;margin-bottom:1.25rem;">
                @lang('simulate.table')
            </h3>

            <div class="amortization-table-wrap" style="box-shadow:var(--shadow-card);">
                <table class="amortization-table">
                    <thead>
                        <tr>
                            <th>@lang('simulate.table_month')</th>
                            <th>@lang('simulate.pay_table')</th>
                            <th>@lang('simulate.table_primal')</th>
                            <th>@lang('simulate.table_interest')</th>
                            <th>@lang('simulate.table_solde')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($amortizationSchedule as $row)
                        <tr>
                            <td>{{ $row['month'] }}</td>
                            <td>{{ number_format($row['payment'], 2, ',', ' ') }} €</td>
                            <td>{{ number_format($row['principal'], 2, ',', ' ') }} €</td>
                            <td>{{ number_format($row['interest'], 2, ',', ' ') }} €</td>
                            <td>{{ number_format(max(0, $row['balance']), 2, ',', ' ') }} €</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="text-center mt-12">
                <a href="{{ route('loan', ['locale' => $locale]) }}" class="btn-primary btn-primary--lg">
                    <i class="fas fa-file-signature"></i> @lang('menu.loan')
                </a>
            </div>
        </div>
        @endisset

    </div>
</section>

@endsection
