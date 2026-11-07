# EDI standards

## Supported today (implemented)

| Standard | Documents | Parser | Generator |
|----------|-----------|--------|-----------|
| X12 | 850, 855, 856, 810, 997 | `X12Parser` | `X12Generator` |
| EDIFACT | ORDERS, DESADV, INVOIC, CONTRL | `EdifactParser` | `EdifactGenerator` |

Fixtures: `tests/Fixtures/edi/`, `database/seeders/data/`.

## Canonical model

Inbound processing maps parsed segments to canonical PHP DTOs:

- `CanonicalPurchaseOrder` (850 / ORDERS)
- `CanonicalShipment` (856 / DESADV)
- `CanonicalInvoice` (810 / INVOIC)

Stored on `edi_transactions.canonical_content`.

## Validation

`EdiValidationService` performs structural checks on parsed arrays. Partner-specific rules can be stored in `edi_validation_rules` (UI/resource planned for full rule designer).

## Planned

- Full guideline compliance packs (retailer-specific 850 maps)
- Additional sets: 846, 864, 940/945
- TRADACOMS / VDA (not started)
