<?php /** @var \Spatie\SslCertificate\SslCertificate $cert */ ?>
SSL Information by domain: {{ $cert->getDomain() }}
Issuer: {!! $cert->getIssuer() !!}
Is valid: {{ $cert->isValid() ? 'yes' : 'no' }}
Is expired: {{ $cert->isExpired() ? 'yes' : 'no' }}
Expiration date: {{ $cert->expirationDate()->format('j M Y') }}
