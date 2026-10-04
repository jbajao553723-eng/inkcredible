@php($signatureError = $errors->signature->first('digital_signature') ?: $errors->verification->first('digital_signature'))
<section class="signature-capture" data-signature-pad data-signature-required="{{ $signatureRequired ? 'true' : 'false' }}">
    <div class="signature-heading">
        <div><h3>Digital signature</h3><p>Draw the signature you will use to sign loan contracts automatically.</p></div>
        <span class="signature-security">Encrypted at rest</span>
    </div>
    @if($existingSignature)
        <div class="signature-existing"><img src="{{ $existingSignature }}" alt="Digital signature currently on file"><span>A signature is already stored. Draw below only if you need to replace it while resubmitting verification.</span></div>
    @endif
    <div class="signature-canvas-wrap">
        <canvas width="720" height="220" data-signature-canvas aria-label="Draw your digital signature"></canvas>
        <span class="signature-line" aria-hidden="true"></span>
    </div>
    <input type="hidden" name="digital_signature" data-signature-input>
    <div class="signature-footer"><p>Your signature is used only after you review a contract and press the signing button.</p><button class="button button-secondary" type="button" data-signature-clear>Clear signature</button></div>
    <p class="signature-error" data-signature-error @if(! $signatureError) hidden @endif>{{ $signatureError ?: 'Please draw your signature before continuing.' }}</p>
</section>
