# Consumer calibration

## Paying — implemented pilot

Current real surface:

- operation: GET /api/payments/{id}
- existing runtime failure: payment-not-found
- existing HTTP status: 404
- existing duplication: controller, service, and local error-response factory

Calibration implementation:

- consumer-owned code: payment-not-found
- consumer-owned type: urn:paying:problem:payment-not-found
- shared DTO: FailureDefinitionDTO
- shared runtime index: FailureRegistry
- shared operation evidence: OperationFailureInventory
- derived evidence: GET /api/payments/{id} + payment.payment_not_found + 404

Result: no Paying-specific branch was added to Failing.

The existing Paying response payload is intentionally unchanged in this pilot. Rendering migration is a separate compatibility step.

## Cruding — read-only calibration candidate

Observed current implementation:

- CrudApiExceptionSubscriber owns Symfony exception interception;
- CrudApiProblemResponseFactory owns local Problem Details rendering;
- explicit mappings include bad request, forbidden, not found, validation failure, generic HttpException status, and internal error.

Expected migration boundary:

- Cruding keeps concrete CRUD failure vocabulary and grammar-backed operation membership;
- Failing supplies declaration/registry/inventory/rendering mechanism;
- generic Failing must not expand Crud route grammar itself.

No mutation was performed because the Cruding worktree contains parallel active changes.

## Billing — read-only calibration candidate

Observed current implementation:

- BillingInvoiceEndpoint chooses 400, 403, 404, 422, and 429 directly;
- BillingErrorPayloadFactory owns concrete business error profiles;
- OpenAPI responses repeat the same HTTP-status knowledge.

Expected migration boundary:

- Billing keeps concrete failure codes, titles, retryability/business metadata, and operation membership;
- Failing supplies the common declaration and inventory mechanism;
- later L5 compares derived runtime status inventory with canonical OpenAPI responses.

No mutation was performed because the Billing worktree contains substantial parallel active changes.

## Calibration finding

The first real consumer does not require a custom FailureCategory abstraction.

A direct standard HTTP status in FailureDefinitionDTO is sufficient for deterministic runtime inventory. A transport-independent semantic category remains deferred until multiple consumers demonstrate a concrete need.

The second calibration candidate exposed a vocabulary-boundary correction: Failing must not require dotted failure codes. Existing consumers already use stable hyphenated and underscored public codes. FailureCode therefore validates only a safe lower-case machine-token envelope while preserving the consumer-owned spelling.
