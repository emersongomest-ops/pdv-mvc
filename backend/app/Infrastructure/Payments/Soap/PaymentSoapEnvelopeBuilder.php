<?php

declare(strict_types=1);

namespace App\Infrastructure\Payments\Soap;

use App\Domain\Payments\DTOs\PaymentRequest;
use App\Domain\Payments\DTOs\RefundRequest;
use DOMDocument;

/**
 * Builds SOAP 1.1 envelopes for acquirer charge/refund (XXE-safe validation).
 */
final class PaymentSoapEnvelopeBuilder
{
    public function chargeEnvelope(PaymentRequest $request, string $namespace): string
    {
        $method = htmlspecialchars($request->method->value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
        $amount = (string) $request->amount;
        $cash = $request->cashReceived !== null ? (string) $request->cashReceived : '';

        $cashXml = $cash !== ''
            ? '<cashReceived>'.$cash.'</cashReceived>'
            : '';

        $body = <<<XML
<Charge xmlns="{$namespace}">
  <method>{$method}</method>
  <amountCents>{$amount}</amountCents>
  {$cashXml}
</Charge>
XML;

        return $this->wrap($body);
    }

    public function refundEnvelope(RefundRequest $request, string $namespace): string
    {
        $amount = (string) $request->amount;
        $reference = htmlspecialchars($request->transactionReference, ENT_XML1 | ENT_QUOTES, 'UTF-8');

        $body = <<<XML
<Refund xmlns="{$namespace}">
  <transactionReference>{$reference}</transactionReference>
  <amountCents>{$amount}</amountCents>
</Refund>
XML;

        return $this->wrap($body);
    }

    public function statusEnvelope(string $transactionReference, string $namespace): string
    {
        $reference = htmlspecialchars($transactionReference, ENT_XML1 | ENT_QUOTES, 'UTF-8');

        $body = <<<XML
<QueryStatus xmlns="{$namespace}">
  <transactionReference>{$reference}</transactionReference>
</QueryStatus>
XML;

        return $this->wrap($body);
    }

    public function assertWellFormed(string $xml): void
    {
        $previous = libxml_use_internal_errors(true);
        $document = new DOMDocument;
        $document->resolveExternals = false;
        $document->substituteEntities = false;
        $loaded = @$document->loadXML($xml, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if ($loaded !== true) {
            throw new \InvalidArgumentException('SOAP envelope is not well-formed XML.');
        }
    }

    private function wrap(string $bodyInner): string
    {
        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    {$bodyInner}
  </soap:Body>
</soap:Envelope>
XML;
    }
}
