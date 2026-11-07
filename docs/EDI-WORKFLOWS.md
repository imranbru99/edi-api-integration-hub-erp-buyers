# EDI workflows

## Inbound purchase order (850)

1. Partner sends 850 (AS2/SFTP/VAN/API upload — transport depends on driver).
2. `EdiInboundProcessor` receives raw interchange.
3. Validation passes → mapping profile optional → canonical PO.
4. Functional ack **997** generated (`AcknowledgmentService`).
5. Downstream: `PurchaseOrder` record creation (API or Filament) — **partial**: seeded in demo; full auto-PO persistence is integration-specific.

## Outbound ASN (856) / invoice (810)

1. Canonical JSON submitted via API or Filament commerce resources.
2. `EdiOutboundProcessor` applies outbound mapping profile.
3. Interchange generated and passed to `DeliveryGatewayInterface`.

## Error handling

Failures and validation issues create `transaction_errors` records. Operators use Filament **Error workbench** (`TransactionErrorResource`) to triage and mark resolved.

## CLI testing

```bash
php artisan edi-hub:process-inbound tests/Fixtures/edi/x12_850_sample.edi --tenant=1 --partner=1 --type=850
```

## Sandbox AS2

Setting `EDI_HUB_DELIVERY_DRIVER=as2` does **not** open AS2 HTTP listeners. Messages are MIME-wrapped and written under `storage/app/as2-out/` (optional OpenSSL PKCS#7 sign when certificate + key configured).
