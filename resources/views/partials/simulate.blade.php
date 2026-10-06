<form action="#" id="loan-calculator-01" data-form-direction="ltr" data-interest-rate="{{ (float) \App\Models\LoanSetting::current()->annual_rate }}"
    class="loan-calculator-form">

    <h3 class="loan-calculator-form__title">{{ __('home.simulate.sectitle') }}</h3>

    <div class="loan-calculator-form__content">

        <div class="input-box__top">
            <span>1 000 €</span>
            <span>50 000 €</span>
        </div>
        <div class="input-box" style="margin-bottom:8px;">
            <div class="range-slider-count" id="loan-calculator-01-count"></div>
            <input type="hidden" class="min-count" id="loan-calculator-01-min-count">
            <input type="hidden" class="max-count" id="loan-calculator-01-max-count">
        </div>

        <div class="input-box__top input-box__top-border">
            <span>1 {{ __('simulate.table_month') }}</span>
            <span>12 {{ __('simulate.table_month') }}</span>
        </div>
        <div class="input-box" style="margin-bottom:20px;">
            <div class="range-slider-month" id="loan-calculator-01-month"></div>
            <input type="hidden" class="min-month" id="loan-calculator-01-min-month">
            <input type="hidden" class="max-month" id="loan-calculator-01-max-month">
        </div>

        <p>
            <span>{{ __('simulate.table_month') }}</span>
            <b><i class="loan-monthly-pay"></i> €</b>
        </p>
        <p>
            <span>{{ __('simulate.terms') }}</span>
            <b><i class="loan-month"></i> {{ __('simulate.table_month') }}</b>
        </p>
        <p>
            <span>{{ __('simulate.total') }}</span>
            <b><i class="loan-total"></i> €</b>
        </p>

        <a href="{{ route('login') }}" class="btn-primary loan-calculator-form__btn"><i class="fas fa-right-to-bracket"></i> @lang('menu.login')</a>


    </div>
</form>
