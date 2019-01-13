<?php /** @var \Spatie\SslCertificate\SslCertificate $cert */ ?>
SSL Information by domain: {{ $cert->getDomain() }}
Issuer: {!! $cert->getIssuer() !!}
Is valid: @if($cert->isValid()) yes @else no @endif
Is expired: @if($cert->isExpired()) yes @else no @endif
Expiration date: {{ $cert->expirationDate()->format('j M Y') }}
