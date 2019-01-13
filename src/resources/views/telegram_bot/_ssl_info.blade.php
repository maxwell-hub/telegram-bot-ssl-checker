<?php /** @var \Spatie\SslCertificate\SslCertificate $cert */ ?>
SSL Information by domain: *{{ $cert->getDomain() }}*
*Issuer:* {{ $cert->getIssuer() }}
*Is valid:* @if($cert->isValid()) yes @else no @endif
*Is expired:* @if($cert->isExpired()) yes @else no @endif
*Valid from:* {{ $cert->validFromDate()->format('j M Y') }}
