@php
    $val      = old($f->key, \App\Services\KycForm::value($user, $f));
    $label    = $f->displayLabel();
    $wide     = in_array($f->type, ['textarea', 'file', 'image'], true) || $f->key === 'address';
    $existing = $f->isFile() ? \App\Services\KycForm::filePath($user, $f) : null;
    $mark     = $f->required ? ' *' : '';
@endphp

@if($f->type === 'country')
<div class="kyc-form-group {{ $wide ? 'span-2' : '' }}" x-data="kycCountry(@js($val))">
  <label for="f_{{ $f->key }}">{{ $label }}{{ $mark }}@unless($f->required) <em>({{ __('onboarding.optional') }})</em>@endunless</label>
  <select id="f_{{ $f->key }}" name="{{ $f->key }}" x-model="country" autocomplete="country-name" @required($f->required)>
    <option value="">— {{ $label }} —</option>
    @if($val)<option value="{{ $val }}" selected>{{ $val }}</option>@endif
    <template x-for="c in countries" :key="c.code">
      <option :value="c.name" x-text="c.name" :selected="c.name === country"></option>
    </template>
  </select>
  <div class="kyc-hint" x-show="detected" style="display:none;color:var(--ca-positive)"><i class="fas fa-location-dot"></i> {{ __('onboarding.country_detected') }}</div>
  @error($f->key)<span class="form-error">{{ $message }}</span>@enderror
</div>

@elseif($f->isFile())
<div class="kyc-form-group span-2">
  <label>{{ $label }}{{ $mark }}@unless($f->required) <em>({{ __('onboarding.optional') }})</em>@endunless</label>
  <label class="kyc-file"><i class="fas {{ $f->key === 'selfie' ? 'fa-camera' : 'fa-cloud-arrow-up' }}"></i>
    <span class="kyc-file__txt" data-default="{{ __('onboarding.doc_choose') }}">{{ $existing ? '✓ ' . __('onboarding.doc_sent') : __('onboarding.doc_choose') }}</span>
    <input type="file" name="{{ $f->key }}" accept="{{ $f->type === 'image' ? 'image/jpeg,image/png' : 'image/jpeg,image/png,application/pdf' }}"
           @if($f->key === 'selfie') capture="user" id="kyc-selfie-input" @endif @required($f->required && ! $existing)>
  </label>
  @if($f->key === 'selfie')
  <div class="kyc-hint">{{ __('kyc.selfie_hint') }}</div>
  <img id="kyc-selfie-preview" class="kyc-selfie-preview" alt="">
  @endif
  @error($f->key)<span class="form-error">{{ $message }}</span>@enderror
</div>

@else
<div class="kyc-form-group {{ $wide ? 'span-2' : '' }}">
  <label for="f_{{ $f->key }}">{{ $label }}{{ $mark }}@unless($f->required) <em>({{ __('onboarding.optional') }})</em>@endunless</label>
  @if($f->type === 'doc_type')
    <select id="f_{{ $f->key }}" name="{{ $f->key }}" @required($f->required)>
      @foreach(['cni' => 'option_cni', 'passeport' => 'option_passeport', 'permis' => 'option_permis'] as $v => $k)
        <option value="{{ $v }}" @selected($val === $v)>{{ __('kyc.' . $k) }}</option>
      @endforeach
    </select>
  @elseif($f->type === 'select')
    <select id="f_{{ $f->key }}" name="{{ $f->key }}" @required($f->required)>
      <option value="">—</option>
      @foreach($f->options ?? [] as $o)<option value="{{ $o }}" @selected($val === $o)>{{ $o }}</option>@endforeach
    </select>
  @elseif($f->type === 'textarea')
    <textarea id="f_{{ $f->key }}" name="{{ $f->key }}" rows="3" @required($f->required)>{{ $val }}</textarea>
  @elseif($f->type === 'date')
    <input type="date" id="f_{{ $f->key }}" name="{{ $f->key }}" value="{{ $val }}" @required($f->required)
           @if($f->builtin) max="{{ $f->key === 'birth_date' ? now()->subDay()->toDateString() : now()->toDateString() }}" @endif>
  @else
    <input type="text" id="f_{{ $f->key }}" name="{{ $f->key }}" value="{{ $val }}" @required($f->required)
           @if($f->key === 'address') autocomplete="street-address" @else autocomplete="off" @endif>
  @endif
  @error($f->key)<span class="form-error">{{ $message }}</span>@enderror
</div>
@endif
